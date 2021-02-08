<?php

require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
require_once DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.product.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/product.lib.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.formother.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/price.lib.php';
require_once DOL_DOCUMENT_ROOT . '/product/dynamic_price/class/price_expression.class.php';

if (!empty($conf->categorie->enabled)) {
    require_once DOL_DOCUMENT_ROOT . '/categories/class/categorie.class.php';
}
require_once DOL_DOCUMENT_ROOT . '/product/dynamic_price/class/price_parser.class.php';

if (!empty($conf->global->PRODUIT_CUSTOMER_PRICES)) {
    require_once DOL_DOCUMENT_ROOT . '/product/class/productcustomerprice.class.php';

    $prodcustprice = new Productcustomerprice($db);
}

//Llamando al Autoload
dol_include_once('/vivescloud/vendor/autoload.php');

class cargarDatos
{

    public function actualizarPrecio($precioCompra,$refe)
    {
        if ($precioCompra && $refe) {

            if (!is_numeric($precioCompra)) {
                echo "error";
                exit;
            }

            $producto = new Product($db);

            $producto->fetch(null, $refe);

            //si el producto esta configurado en euros o dolares
            if ($producto->array_options["options_moneda"] != null) {

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
            }
            // Multiprices
            if (!$error && !empty($conf->global->PRODUIT_MULTIPRICES)) {

                for ($i = 1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++) {

                    if ($i == 1) {
                        if ($precio1 > 0) {
                            $newprice[$i] = $precio1;
                        }
                    }

                    if ($i == 2) {
                        if ($precio2 > 0) {
                            $newprice[$i] = $precio2;
                        }
                    }

                    if ($i == 3) {
                        if ($precio3 > 0) {
                            $newprice[$i] = $precio3;
                        }
                    }

                    $pricestoupdate[$i] = array(
                        'price' => $newprice[$i],
                        'price_min' => $newprice[$i],
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

                echo json_encode($producto_array);

            } else {
                $db->rollback();
            }
        }
    }
}
