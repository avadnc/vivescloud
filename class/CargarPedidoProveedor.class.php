<?php

class CargarPedidoProveedor
{
    public function cargarLinea($pedido, $idProd, $qty, $cost_price)
    {
        global $db;

        $pedidoProv = new CommandeFournisseur($db);
        $producto = new Product($db);

        $pedidoProv->fetch($pedido);
		$producto->fetch($idProd);
		// echo '<pre>';
		// print_r($pedido->lines);
		// echo '</pre>';
		// exit;

        $result = $pedidoProv->addline(
            $producto->label,
            $cost_price,
            $qty,
            $producto->tva_tx,
            $producto->localtax1_tx,
            $producto->localtax2_tx,
            $producto->id,
            0, // We already have the $idprod always defined
            $producto->ref_supplier,
            0,
            $producto->price_base_type,
            $cost_price * 1.16,
            $producto->type,
            $producto->tva_npr,
            '',
            null,
            null,
            null,
            null,
            $cost_price,
            '',
            0
        );

        return $result;
    }

    public function leerPedido($pedido)
    {
        global $db;

        $pedidoProv = new CommandeFournisseur($db);
        $pedidoProv->fetch($pedido);

        $pedidoLineas = [];

        $totalLineas = count($pedidoProv->lines);

		foreach($pedidoProv->lines as $key => $value){
			     $pedidosLineasArray = [
                'ref' => $value->ref,
                'desc' => $value->product_label,
                'qty' => $value->qty,
                'price' => $value->subprice,
                // 'id' => $value->id,
            ];
            array_push($pedidoLineas, $pedidosLineasArray);
		}

        return $pedidoLineas;

    }
}
