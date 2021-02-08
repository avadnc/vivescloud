<?php

/*
 * Requirements: https: //github.com/mjacobus/php-query-builder
 */
class ConsultasProductos
{
//Functions
    public function cargarCategorias()
    {

        global $db;
        if ($conf->MULTICOMPANY_PRODUCT_SHARING_ENABLED) {

            $campos = ['rowid', 'label'];
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->select($campos);
            $consulta->from(MAIN_DB_PREFIX . 'categorie');
            $consulta->where([
                ['type', 0, '='],
            ]);

        } else {

            $campos = ['rowid', 'label'];
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->select($campos);
            $consulta->from(MAIN_DB_PREFIX . 'categorie');
            $consulta->where([
                ['type', 0, '='],
                ['entity', $conf->entity, '='],
            ]);

        }

        $resql = $db->query($consulta->toSql());
        $num = $db->num_rows($resql);
        $categorias = [];
        if ($num > 0) {
            $i = 0;
            while ($i < $num) {
                $obj = $db->fetch_object($resql);
                if ($obj) {
                    $array_categoria = [
                        'id' => $obj->rowid,
                        'categoria' => $obj->label,
                    ];
                    array_push($categorias, $array_categoria);
                }
                $i++;
            }
            return $categorias;
        }
    }
    public function cargarPorMarca($marca)
    {
        global $db;

        if ($conf->MULTICOMPANY_PRODUCT_SHARING_ENABLED) {

            $campos = array(MAIN_DB_PREFIX . 'product.rowid');
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->select($campos);
            $consulta->from(MAIN_DB_PREFIX . 'categorie_product');
            $consulta->innerJoin(MAIN_DB_PREFIX . 'product', 'fk_product = rowid');
            $consulta->where([
                [MAIN_DB_PREFIX . 'categorie_product.fk_categorie', $marca, '='],

            ]);
        } else {

            $campos = array(MAIN_DB_PREFIX . 'product.rowid');
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->select($campos);
            $consulta->from(MAIN_DB_PREFIX . 'categorie_product');
            $consulta->innerJoin(MAIN_DB_PREFIX . 'product', 'fk_product = rowid');
            $consulta->where([
                [MAIN_DB_PREFIX . 'categorie_product.fk_categorie', $marca, '='],
                [MAIN_DB_PREFIX . 'product.entity', $conf->entity, '='],
            ]);
        }

        $resql = $db->query($consulta->toSql());
        $num = $db->num_rows($resql);
        $productos = [];
        if ($num) {
            $i = 0;
            while ($i < $num) {
                $obj = $db->fetch_object($resql);
                if ($obj) {
                    // You can use here results
                    $product = new Product($db);

                    $product->fetch($obj->rowid);
                    $producto_array = [
                        'id' => $product->id,
                        'ref' => $product->ref,
                        'label' => $product->label,
                        'moneda' => $product->array_options["options_moneda"],
                        'precio_moneda' => $product->array_options["options_precio_moneda"],
                        'cost_price' => round($product->cost_price, 2),
                        'margen1' => $product->array_options["options_margen1"],
                        'precio1' => round($product->multiprices[1], 2),
                        'margen2' => $product->array_options["options_margen2"],
                        'precio2' => round($product->multiprices[2], 2),
                        'margen3' => $product->array_options["options_margen3"],
                        'precio3' => round($product->multiprices[3], 2),

                    ];
                    array_push($productos, $producto_array);
                }
                $i++;

            }
        }

        return $productos;

    }

    public function cargarProductos($referencia)
    {
        global $db, $conf;

        if ($conf->MULTICOMPANY_PRODUCT_SHARING_ENABLED) {

            $campos = array('rowid');
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->select($campos);
            $consulta->from(MAIN_DB_PREFIX . 'product');
            $consulta->where([
                ['ref', '%' . $referencia . '%', 'LIKE'],
                ['tosell', 1, '='],
            ]);

        } else {

            $campos = array('rowid');
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->select($campos);
            $consulta->from(MAIN_DB_PREFIX . 'product');
            $consulta->where([
                ['ref', '%' . $referencia . '%', 'LIKE'],
                ['entity', $conf->entity, '='],
                ['tosell', 1, '='],
            ]);
        }

        $resql = $db->query($consulta->toSql());
        $num = $db->num_rows($resql);

        if ($num == 0) {

            if ($conf->MULTICOMPANY_PRODUCT_SHARING_ENABLED) {
                $campos = array('rowid');
                $consulta = PO\QueryBuilder::factorySelect();
                $consulta->select($campos);
                $consulta->from(MAIN_DB_PREFIX . 'product');
                $consulta->where([
                    ['label', '%' . $referencia . '%', 'LIKE'],
                    ['tosell', 1, '='],
                ]);

            } else {

                $campos = array('rowid');
                $consulta = PO\QueryBuilder::factorySelect();
                $consulta->select($campos);
                $consulta->from(MAIN_DB_PREFIX . 'product');
                $consulta->where([
                    ['label', '%' . $referencia . '%', 'LIKE'],
                    ['entity', $conf->entity, '='],
                    ['tosell', 1, '='],
                ]);

            }

            $resql = $db->query($consulta->toSql());
            $num = $db->num_rows($resql);

            if ($num == 0) {

                if ($conf->MULTICOMPANY_PRODUCT_SHARING_ENABLED) {

                    $campos = array('rowid');
                    $consulta = PO\QueryBuilder::factorySelect();
                    $consulta->select($campos);
                    $consulta->from(MAIN_DB_PREFIX . 'product');
                    $consulta->where([
                        ['description', '%' . $referencia . '%', 'LIKE'],
                        ['tosell', 1, '='],
                    ]);

                } else {

                    $campos = array('rowid');
                    $consulta = PO\QueryBuilder::factorySelect();
                    $consulta->select($campos);
                    $consulta->from(MAIN_DB_PREFIX . 'product');
                    $consulta->where([
                        ['description', '%' . $referencia . '%', 'LIKE'],
                        ['entity', $conf->entity, '='],
                        ['tosell', 1, '='],
                    ]);

                }

                $resql = $db->query($consulta->toSql());
                $num = $db->num_rows($resql);
            }

        }

        $productos = [];
        if ($num) {
            $i = 0;
            while ($i < $num) {
                $obj = $db->fetch_object($resql);
                if ($obj) {
                    // You can use here results
                    $product = new Product($db);

                    $product->fetch($obj->rowid);
                    $producto_array = [
                        'id' => $product->id,
                        'ref' => $product->ref,
                        'label' => $product->label,
                        'moneda' => $product->array_options["options_moneda"],
                        'precio_moneda' => $product->array_options["options_precio_moneda"],
                        'cost_price' => round($product->cost_price, 2),
                        'margen1' => $product->array_options["options_margen1"],
                        'precio1' => round($product->multiprices[1], 2),
                        'margen2' => $product->array_options["options_margen2"],
                        'precio2' => round($product->multiprices[2], 2),
                        'margen3' => $product->array_options["options_margen3"],
                        'precio3' => round($product->multiprices[3], 2),

                    ];
                    array_push($productos, $producto_array);
                }
                $i++;

            }
        }

        return array_values($productos);
    }

    public function insertarMargenes($precioCompra, $refe)
    {
        global $db, $conf, $user;

        if (!is_numeric($precioCompra)) {
            echo "error";
            exit;
        }

        $producto = new Product($db);

        $producto->fetch(null, $refe);
        $productoProveedor = new ProductFournisseur($db);
        $productoProveedor->fetch(null,$refe);

//si el producto esta configurado en euros o dolares
        if ($producto->array_options["options_moneda"] == 'EUR' || $producto->array_options["options_moneda"] == 'USD') {

            $campos = array('label');
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->from(MAIN_DB_PREFIX . 'c_cfdimx_tipocambiocfdi');
            $consulta->where([
                ['code', $producto->array_options["options_moneda"], '='],
                ['active', 1, '='],
            ]);

            $resql = $db->query($consulta->toSql());
            $num = $db->num_rows($resql);
            if ($num > 0) {
                $obj = $db->fetch_object($resql);

                $margen1 = $producto->array_options["options_margen1"];
                $margen2 = $producto->array_options["options_margen2"];
                $margen3 = $producto->array_options["options_margen3"];
//asignar valores
                $precioCosto = round(price2num($precioCompra) * price2num($obj->label), 2);
                $producto->cost_price = $precioCosto;

                if ($margen1) {

                    $precio1 = $precioCosto * (1 + ($margen1 / 100));

                }
                if ($margen2) {

                    $precio2 = $precioCosto * (1 + ($margen2 / 100));

                }
                if ($margen3) {

                    $precio3 = $precioCosto * (1 + ($margen3 / 100));

                }

            }
            $producto->array_options["options_precio_moneda"] = $precioCompra;
        } else {

            $margen1 = $producto->array_options["options_margen1"];
            $margen2 = $producto->array_options["options_margen2"];
            $margen3 = $producto->array_options["options_margen3"];

            //asignar valores

            $precioCosto = round(price2num($precioCompra), 2);
            $producto->cost_price = $precioCosto;

            if ($margen1) {

                $precio1 = $precioCosto * (1 + ($margen1 / 100));

            }
            if ($margen2) {

                $precio2 = $precioCosto * (1 + ($margen2 / 100));

            }
            if ($margen3) {

                $precio3 = $precioCosto * (1 + ($margen3 / 100));

            }

            //precio minimo basado en el 8 de felipe davila
            $preciominimo = $precioCosto * (1 + (8 / 100));

        }
// Multiprices
        if (!$error && !empty($conf->global->PRODUIT_MULTIPRICES)) {

            for ($i = 1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++) {

                if ($i == 1) {
                    if ($precio1 > 0) {
                        $newprice[$i] = $precio1;
                        $newminprice[$i] = $preciominimo;
                    }
                }

                if ($i == 2) {
                    if ($precio2 > 0) {
                        $newprice[$i] = $precio2;
                        $newminprice[$i] = $preciominimo;

                    }
                }

                if ($i == 3) {
                    if ($precio3 > 0) {
                        $newprice[$i] = $precio3;
                        $newminprice[$i] = $preciominimo;
                    }
                }

                $pricestoupdate[$i] = array(
                    'price' => $newprice[$i],
                    'price_min' => $newminprice[$i],
                    'price_base_type' => $producto->price_base_type,
                    'vat_tx' => $producto->tva_tx,
                    'npr' => $producto->tva_npr,
                    'localtaxes_array' => array('0' => 0, '1' => 0, '2' => 0, '3' => 0),
                );

            }
        }

        $db->begin();

        foreach ($pricestoupdate as $key => $val) {

            $newprice = $val['price'];

            if ($val['price'] < $val['price_min'] && !empty($producto->fk_price_expression)) {
                $newprice = $val['price_min']; //Set price same as min, the user will not see the
            }

            $newprice = price2num($newprice, 'MU');
            $newprice_min = price2num($val['price_min'], 'MU');

            $res = $producto->updatePrice($newprice, $val['price_base_type'], $user, $val['vat_tx'], $newprice_min, $key, $val['npr'], 0, 0, $val['localtaxes_array']);

            if ($res < 0) {
                $error++;
                setEventMessages($producto->error, $producto->errors, 'errors');
                break;
            }
        }

        $resultado = $producto->update($producto->id, $user);
        if ($resultado > 0) {
            $db->commit();

            $producto->fetch($producto->id);
            $producto_array = [

                'precio1' => round($producto->multiprices[1], 2),
                'precio2' => round($producto->multiprices[2], 2),
                'precio3' => round($producto->multiprices[3], 2),
            ];

            return $producto_array;

        } else {
            $db->rollback();
        }

    }
}
