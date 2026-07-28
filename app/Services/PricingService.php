<?php

namespace App\Services;

use App\Models\Event;

/**
 * Calcula el MEJOR precio para una cantidad de fotos combinando el precio
 * por foto individual con los paquetes del evento. Es la fuente de verdad:
 * el mismo cálculo corre en el navegador (para mostrar el total en vivo) y
 * aquí en el servidor (para registrar el pedido), así el cliente nunca ve un
 * precio distinto al que se guarda.
 *
 * Algoritmo: programación dinámica (unbounded knapsack). Se pueden usar
 * varios paquetes y combinarlos con fotos sueltas; además se contempla comprar
 * un paquete que "sobra" (cubre más fotos de las elegidas) si sale más barato.
 *
 * @return array{sub:float,total:float,discount:float,applied_label:?string}
 */
class PricingService
{
    public function quote(Event $event, int $count): array
    {
        $unit = (float) $event->price_unit;
        $count = max(0, $count);

        if ($count === 0) {
            return ['sub' => 0.0, 'total' => 0.0, 'discount' => 0.0, 'applied_label' => null];
        }

        $packages = $event->packages
            ->map(fn ($p) => ['qty' => (int) $p->qty, 'price' => (float) $p->price, 'label' => $p->label])
            ->filter(fn ($p) => $p['qty'] > 0 && $p['price'] > 0)
            ->values()
            ->all();

        $sub = round($count * $unit, 2);

        // DP: cost[i] = costo mínimo para exactamente i fotos.
        $cost = array_fill(0, $count + 1, INF);
        $cost[0] = 0.0;
        for ($i = 1; $i <= $count; $i++) {
            $cost[$i] = $cost[$i - 1] + $unit; // agregar una foto suelta
            foreach ($packages as $pk) {
                if ($i >= $pk['qty']) {
                    $cost[$i] = min($cost[$i], $cost[$i - $pk['qty']] + $pk['price']);
                }
            }
        }

        $total = $cost[$count];

        // Sobrecobertura: comprar un paquete más grande que cubra TODA la selección
        // si resulta más barato (ej: elijo 9 fotos pero el paquete de 10 es más barato).
        $overshootLabel = null;
        foreach ($packages as $pk) {
            if ($pk['qty'] >= $count && $pk['price'] < $total) {
                $total = $pk['price'];
                $overshootLabel = $pk['label'] ?: ('Paquete ' . $pk['qty'] . ' fotos');
            }
        }

        $total = round($total, 2);
        $discount = round($sub - $total, 2);

        $label = null;
        if ($overshootLabel !== null) {
            $label = $overshootLabel;
        } elseif ($discount > 0.001) {
            // ¿un único paquete cubre exactamente la cantidad?
            $exact = collect($packages)->firstWhere('qty', $count);
            $label = $exact
                ? ($exact['label'] ?: ('Paquete ' . $exact['qty'] . ' fotos'))
                : 'Precio con paquetes';
        }

        return [
            'sub'           => $sub,
            'total'         => $total,
            'discount'      => max(0.0, $discount),
            'applied_label' => $label,
        ];
    }
}
