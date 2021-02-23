<?php

class MovimientoStocksProductos
{

    private function is_negative_number($number = 0)
    {

        if (is_numeric($number) and ($number < 0)) {
            return true;
        } else {
            return false;
        }

    }

    private function consultarPedimento($lote, $almacen, $producto)
    {
        global $db;

        $campos = ['p.rowid AS idproduct', 'ps.rowid AS idstock', 'pb.rowid AS idbatch', 'pb.batch', 'pb.qty', 'ps.reel AS stock'];
        $select = new PO\QueryBuilder\Statements\Select();
        $select->select($campos);
        $select->from(MAIN_DB_PREFIX . 'product_batch AS pb');
        $select->innerJoin(MAIN_DB_PREFIX . 'product_stock as ps', 'pb.fk_product_stock = ps.rowid');
        $select->innerJoin(MAIN_DB_PREFIX . 'product as p', 'ps.fk_product = p.rowid');
        if (!isset($lote)) {

            $select->where('ps.fk_entrepot', $almacen, '=');
            $select->where('p.rowid', $producto, '=');

            $result = $db->query($select->toSql());
            $num = $db->num_rows($result);

            //devolver la suma de los pedimentos

            $totalStock = 0;
            $totalPedimento = 0;

            if ($num > 0) {
                $i = 0;

                while ($i < $num) {

                    $obj = $db->fetch_object($result);

                    $totalPedimento += $obj->qty;
                    $totalStock = $obj->stock;
                    $i++;

                }

                return [
                    'totalstock' => intval($totalStock),
                    'totalpediment' => $totalPedimento,
                ];

            } else {

                return null;

            }

        } else {
            $select->where('pb.batch', $lote, '=');
            $select->where('ps.fk_entrepot', $almacen, '=');
            $select->where('p.rowid', $producto, '=');

            $result = $db->query($select->toSql());
            $num = $db->num_rows($result);

            //existe pedimento
            if ($num > 0) {

                return $obj = $db->fetch_object($result);

            } else {

                return null;

            }
        }

    }

    private function consultarStock($producto, $almacen)
    {
        global $db;

        $campos = ['rowid', 'reel'];
        $select = new PO\QueryBuilder\Statements\Select();
        $select->select($campos);
        $select->from(MAIN_DB_PREFIX . 'product_stock');
        $select->where('fk_product', $producto, '=');
        $select->where('fk_entrepot', $almacen, '=');

        $result = $db->query($select->toSql());
        $num = $db->num_rows($result);

        if ($num > 0) {

            return $obj = $db->fetch_object($result);

        } else {

            return null;
        }

    }

    private function actualizarTabla($tabla, $id, $campovalor = [])
    {
        global $db;

        $update = new PO\QueryBuilder\Statements\Update;
        $update->table(MAIN_DB_PREFIX . $tabla);
        $update->set($campovalor)->where('rowid', ':rowid');

        $result = $db->query($update->toSql(array('rowid' => $id)));

        if ($result > 0) {

            return true;

        } else {

            return false;

        }

    }

    private function insertarDatos($tabla, $datos = [])
    {
        global $db;
        $insert = PO\QueryBuilder::insert();
        $insert->into(MAIN_DB_PREFIX . $tabla)->values($datos);

        $result = $db->query($insert->toSql());

        if ($result > 0) {

            return true;

        } else {

            return false;

        }

    }

    public function corregirStock($accion, $producto, $lote, $almacen, $cantidad)
    {

        global $db, $user;

        if (empty($producto) || empty($almacen) || empty($cantidad)) {

            return [
                'code' => 'Error',
                'msg' => 'Falta parámetro para poder continuar',
            ];

        } else {

            if (!empty($lote)) {

                //SELECT p.rowid AS idproduct, ps.rowid AS idstock,pb.rowid AS idbatch, pb.batch, pb.qty
                //FROM llx_product_batch AS pb
                //INNER JOIN llx_product_stock as ps ON pb.fk_product_stock = ps.rowid
                //INNER JOIN llx_product AS p ON ps.fk_product = p.rowid WHERE pb.batch = 'xcxzvzxcv<z' AND ps.fk_entrepot = 1;

                $consulta = $this->consultarPedimento($lote, $almacen, $producto);
                //existe pedimento
                if ($consulta != null) {

                    if ($accion == "sumar") {

                        $product = new Product($db);
                        $product->fetch($producto);

                        $now = dol_now();
                        // $result = $product->correct_stock(
                        //     $user,
                        //     $almacen,
                        //     $cantidad,
                        //     0,
                        //     'Correción de sotck del producto ' . $product->ref,
                        //     0,
                        //     $now . ' - ' . $product->ref,
                        //     $origin_element = '',
                        //     $origin_id = null

                        // ); // We do not change value of stock for a correction
                        $result = $product->correct_stock_batch(
                            $user,
                            $almacen,
                            $cantidad,
                            0,
                            'Correción de sotck del producto ' . $product->ref,
                            0,
                            null,
                            null,
                            $lote,
                            $now . ' - ' . $product->ref,
                            $origin_element = '',
                            $origin_id = null
                        ); // We do not change value of stock for a correction

                        if ($result > 0) {

                            return [
                                'code' => 'Ok',
                                'msg' => 'Datos Actualizados Correctamente',
                            ];
                        }
                    }

                    if ($accion == "restar") {

                        $actualizar = null;
                        $datosActualiza = null;

                        if ($this->is_negative_number($consulta->qty - $cantidad)) {
                            return [
                                'code' => 'error',
                                'msg' => 'No se pueden quitar mas unidades de las que hay en el pedimento',
                            ];

                        }
                        $datosActualiza = [
                            'reel' => $consulta->stock - $cantidad,
                        ];

                        $actualizar = $this->actualizarTabla('product_stock', $consulta->idstock, $datosActualiza);
                        $datosActualiza = null;

                        if ($actualizar == true) {

                            $datosActualiza = [
                                'qty' => $consulta->qty - $cantidad,
                            ];

                            $actualizar = $this->actualizarTabla('product_batch', $consulta->idbatch, $datosActualiza);

                            if ($actualizar == true) {
                                return [
                                    'code' => 'Ok',
                                    'msg' => 'Datos Actualizados Correctamente',
                                ];
                            }
                        }

                    } // else {} // añadir el pedimento a mano

                } else {

                    $stock = $this->consultarStock($producto, $almacen);
                    $pedimentoQty = $this->consultarPedimento(null, $almacen, $producto);
                    $stocktotal = 0;
                    $stocktotalpedimento = 0;
                    if ($accion == 'sumar') {

                        $product = new Product($db);
                        $product->fetch($producto);

                        $now = dol_now();
                        $result = $product->correct_stock(
                            $user,
                            $almacen,
                            $cantidad,
                            0,
                            'Correción de sotck del producto ' . $product->ref,
                            0,
                            $now . ' - ' . $product->ref,
                            $origin_element = '',
                            $origin_id = null

                        ); // We do not change value of stock for a correction

                        if ($result > 0) {

                            $stockactual = $this->consultarStock($product->id, $almacen);
                            $datosInsertar = [
                                'fk_product_stock' => $stockactual->rowid,
                                'batch' => $lote,
                                'qty' => $cantidad,
                            ];
                            $resp = $this->insertarDatos('product_batch', $datosInsertar);
                            if ($resp == true) {

                                return [
                                    'code' => 'Ok',
                                    'msg' => 'Datos Actualizados Correctamente',
                                ];

                            } else {

                                return [
                                    'code' => 'error',
                                    'msg' => 'No se puede añadir corrija stock',
                                ];

                            }

                        }

                    }
                    // else {

                    //     $product = new Product($db);
                    //     $product->fetch($producto);

                    //     $now = dol_now();
                    //     $result = $product->correct_stock(
                    //         $user,
                    //         $almacen,
                    //         $cantidad,
                    //         0,
                    //         'Correción de sotck del producto ' . $product->ref,
                    //         0,
                    //         $now . ' - ' . $product->ref,
                    //         $origin_element = '',
                    //         $origin_id = null

                    //     ); // We do not change value of stock for a correction

                    //     if ($result > 0) {

                    //         $stockactual = $this->consultarStock($product->id, $almacen);
                    //         $datosInsertar = [
                    //             'fk_product_stock' => $stockactual->rowid,
                    //             'batch' => $lote,
                    //             'qty' => $cantidad,
                    //         ];
                    //         $resp = $this->insertarDatos('product_batch', $datosInsertar);
                    //         if ($resp == true) {

                    //             return [
                    //                 'code' => 'Ok',
                    //                 'msg' => 'Datos Actualizados Correctamente',
                    //             ];

                    //         } else {

                    //             return [
                    //                 'code' => 'error',
                    //                 'msg' => 'No se puede añadir corrija stock',
                    //             ];

                    //         }

                    //     }
                    // }
                }
            }
        }

    }

    public function transferirStock($origen, $destino, $lote, $producto, $cantidad)
    {
        global $db;

        if (empty($origen) || empty($destino) || empty($lote) || empty($producto) || empty($cantidad)) {
            return [
                'code' => 'Error',
                'msg' => 'Falta parámetro para poder continuar',
            ];

        } else {

            $consultaOrigen = $this->consultarPedimento($lote, $origen, $producto);

            if ($consultaOrigen != null) {

                if ($consultaOrigen->qty >= $cantidad) {

                    $consultaDestino = $this->consultarPedimento($lote, $destino, $producto);

                    if ($consultaDestino != null) {
                        //Actualizamos totales origen

                        $productoStockDestino = $this->consultarStock($producto, $destino);
                        $productoStockOrigen = $this->consultarStock($producto, $origen);

                        $datosActualiza = [
                            'reel' => $productoStockOrigen->reel - $cantidad,
                            'fk_entrepot' => $origen,
                        ];

                        $actualizaOrigen = $this->actualizarTabla('product_stock', $productoStockOrigen->rowid, $datosActualiza);

                        //actualizamos lotes origen

                        $datosActualiza = [
                            'qty' => $consultaOrigen->qty - $cantidad,
                        ];

                        $actualizaLoteOrigen = $this->actualizarTabla('product_batch', $consultaOrigen->idbatch, $datosActualiza);

                        //actualizamos stock destino

                        $datosActualiza = [
                            'reel' => $productoStockDestino->reel + $cantidad,
                            'fk_entrepot' => $destino,
                        ];

                        $actualizaDestino = $this->actualizarTabla('product_stock', $productoStockDestino->rowid, $datosActualiza);

                        //actualizamos lote destino

                        $datosActualiza = [
                            'qty' => $consultaDestino->qty + $cantidad,
                        ];

                        $actualizaLoteDestino = $this->actualizarTabla('product_batch', $consultaDestino->idbatch, $datosActualiza);

                        if ($actualizaDestino == true && $actualizaLoteDestino == true) {

                            return [
                                'code' => 'Ok',
                                'msg' => 'Transferencia de Productos de ' . $origen . ' a ' . $destino . ' ha sido exitosa',
                            ];
                            exit;

                        } else {
                            return [
                                'code' => 'error',
                                'msg' => 'Transferencia de Productos de ' . $origen . ' a ' . $destino . ' ha fallado',
                            ];
                            exit;

                        }

                    } else {

                        //no existe pedimento, verificamos si existe cantidad producto en almacen

                        $productoStockDestino = $this->consultarStock($producto, $destino);
                        $productoStockOrigen = $this->consultarStock($producto, $origen);

                        if ($productoStockDestino != null) { //OK TODO
                            //Si tenemos stock en el almacen de destino para actualizar totales

                            $datosActualiza = [
                                'reel' => $productoStockDestino->reel + $cantidad,
                                'fk_entrepot' => $destino,
                            ];

                            $actualizaDestino = $this->actualizarTabla('product_stock', $productoStockDestino->rowid, $datosActualiza);

                            if ($actualizaDestino == true) {

                                //decrementamos totales

                                $datosActualiza = [
                                    'reel' => $productoStockOrigen->reel - $cantidad,
                                    'fk_entrepot' => $origen,
                                ];

                                $actualizaOrigen = $this->actualizarTabla('product_stock', $productoStockOrigen->rowid, $datosActualiza);

                                if ($actualizaOrigen == true) {

                                    //Hacemos insert del pedimento con la cantidad deseeada
                                    $insertarDatos = [
                                        'fk_product_stock' => $productoStockDestino->rowid,
                                        'batch' => $lote,
                                        'qty' => $cantidad,
                                    ];
                                    $resultado = $this->insertarDatos('product_batch', $insertarDatos);

                                    //retiramos cantidad del pedimento de origen

                                    $datosActualiza = [
                                        'qty' => $consultaOrigen->qty - $cantidad,
                                    ];

                                    $actualizaLoteOrigen = $this->actualizarTabla('product_batch', $consultaOrigen->idbatch, $datosActualiza);

                                    if ($resultado == true && $actualizaLoteOrigen == true) {

                                        return [
                                            'code' => 'Ok',
                                            'msg' => 'Transferencia de Productos de ' . $origen . ' a ' . $destino . ' ha sido exitosa',
                                        ];
                                        exit;

                                    } else {
                                        return [
                                            'code' => 'error',
                                            'msg' => 'Transferencia de Productos de ' . $origen . ' a ' . $destino . ' ha fallado',
                                        ];
                                        exit;

                                    }

                                }
                            }

                        } else {
                            //no existe, añadimos producto en el almacen y damos de alta pedimento
                            $altaDatos = [
                                'fk_product' => $producto,
                                'fk_entrepot' => $destino,
                                'reel' => $cantidad,
                            ];

                            $altaStocks = $this->insertarDatos('product_stock', $altaDatos);

                            $productoStockOrigen = $this->consultarStock($producto, $origen);

                            $datosActualiza = [
                                'reel' => $productoStockOrigen->reel - $cantidad,
                                'fk_entrepot' => $origen,
                            ];

                            $actualizaOrigen = $this->actualizarTabla('product_stock', $productoStockOrigen->rowid, $datosActualiza);

                            if ($altaStocks == true && $actualizaOrigen == true) {

                                $productoDestino = $this->consultarStock($producto, $destino);

                                $altaDatos = [
                                    'fk_product_stock' => $productoDestino->rowid,
                                    'qty' => $cantidad,
                                    'batch' => $lote,
                                ];

                                $altaLote = $this->insertarDatos('product_batch', $altaDatos);

                                $datosActualiza = [
                                    'qty' => $consultaOrigen->qty - $cantidad,
                                ];

                                $actualizaLoteOrigen = $this->actualizarTabla('product_batch', $consultaOrigen->idbatch, $datosActualiza);

                                if ($altaLote == true && $actualizaLoteOrigen == true) {
                                    return [
                                        'code' => 'Ok',
                                        'msg' => 'Transferencia de Productos de ' . $origen . ' a ' . $destino . ' ha sido exitosa',
                                    ];

                                } else {
                                    return [
                                        'code' => 'error',
                                        'msg' => 'Transferencia de Productos de ' . $origen . ' a ' . $destino . ' ha fallado',
                                    ];

                                }
                            }

                        }

                    }

                    //Si sale 0 hay que dar de alta el pedimento y el stock

                } else {
                    //No se puede enviar mas cantidad de la que existe
                    return [
                        'code' => 'Error',
                        'msg' => 'No se puede enviar mas cantidad de la que existe',
                    ];

                }
            }

        }

    }

}
