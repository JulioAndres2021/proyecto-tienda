<?php

namespace Tests\Unit;

use App\Services\CalculadoraCompraService;
use PHPUnit\Framework\TestCase;

class CalculadoraCompraServiceTest extends TestCase
{
    public function test_calcula_correctamente_el_total_con_envio(): void
    {
        $service = new CalculadoraCompraService();

        $resultado = $service->calcular(10000);

        $this->assertEquals(10000, $resultado['subtotal']);
        $this->assertEquals(2100, $resultado['impuestos']);
        $this->assertEquals(5000, $resultado['costo_envio']);
        $this->assertEquals(17100, $resultado['total']);
    }

    public function test_no_cobra_envio_si_supera_el_monto_minimo(): void
    {
        $service = new CalculadoraCompraService();

        $resultado = $service->calcular(50000);

        $this->assertEquals(50000, $resultado['subtotal']);
        $this->assertEquals(10500, $resultado['impuestos']);
        $this->assertEquals(0, $resultado['costo_envio']);
        $this->assertEquals(60500, $resultado['total']);
    }

    public function test_subtotal_cero_no_genera_cargos(): void
    {
        $service = new CalculadoraCompraService();

        $resultado = $service->calcular(0);

        $this->assertEquals(0, $resultado['subtotal']);
        $this->assertEquals(0, $resultado['impuestos']);
        $this->assertEquals(0, $resultado['costo_envio']);
        $this->assertEquals(0, $resultado['total']);
    }
}