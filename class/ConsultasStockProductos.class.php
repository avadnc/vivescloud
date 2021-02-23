<?php
/*
 * Requirements: https://github.com/mjacobus/php-query-builder
 */
class ConsultaStockProductos
{

    //Cargar los almacenes
    public function cargarAlmacen($todos = false)
    {

        global $db, $conf;

        $campos = ['rowid', 'ref'];
        $consulta = PO\QueryBuilder::factorySelect();
        $consulta->select($campos);
        $consulta->from(MAIN_DB_PREFIX . 'entrepot');

        if ($todos == false) {

            $consulta->where([
                ['statut', 1, '='],
                $conf->MULTICOMPANY_PRODUCT_SHARING_ENABLED ? [] : ['entity', $conf->entity, '='],
            ]);

        } else {

            $consulta->where([
                ['statut', 1, '='],
            ]);

        }

        $resql = $db->query($consulta->toSql());
        $num = $db->num_rows($resql);
        $almacenes = [];
        if ($num > 0) {
            $i = 0;
            while ($i < $num) {
                $obj = $db->fetch_object($resql);
                if ($obj) {
                    $array_almacen = [
                        'id' => $obj->rowid,
                        'almacen' => $obj->ref,
                    ];
                    array_push($almacenes, $array_almacen);
                }
                $i++;
            }
            return $almacenes;
        }

    }

    public function cargarProducto($id = null, $ref = null, $marca = null)
    {
        global $db, $conf;
        $campos = array(MAIN_DB_PREFIX . 'product.rowid');
        $consulta = PO\QueryBuilder::factorySelect();
        $consulta->select($campos);
        $consulta->from(MAIN_DB_PREFIX . 'product');

        if ($id != null) {

            if ($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED == 1) {
                $consulta->where([
                    ['rowid', $id, '='],
                    ['tosell', 1, '='],
                ]);
            } else {
                $consulta->where([
                    ['rowid', $id, '='],
                    ['entity', $conf->entity, '='],
                    ['tosell', 1, '='],
                ]);
            }

        }

        if ($ref != null) {
            if ($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED == 1) {

                $consulta->where([
                    ['ref LIKE "%' . $ref . '%" OR label LIKE "%' . $ref . '%" OR description LIKE "%' . $ref . '%"'],
                    ['tosell', 1, '='],
                ]);

            } else {

                $consulta->where([
                    ['ref LIKE "%' . $ref . '%" OR label LIKE "%' . $ref . '%" OR description LIKE "%' . $ref . '%"'],
                    ['entity', $conf->entity, '='],
                    ['tosell', 1, '='],
                ]);

            }

        }

        if ($marca != null) {

            if ($conf->global->MULTICOMPANY_PRODUCT_SHARING_ENABLED == 1) {

                $consulta->from(MAIN_DB_PREFIX . 'categorie_product');
                $consulta->innerJoin(MAIN_DB_PREFIX . 'product', 'fk_product = rowid');
                $consulta->where([
                    [MAIN_DB_PREFIX . 'categorie_product.fk_categorie', $marca, '='],
                ]);

            } else {

                $consulta->from(MAIN_DB_PREFIX . 'categorie_product');
                $consulta->innerJoin(MAIN_DB_PREFIX . 'product', 'fk_product = rowid');
                $consulta->where([
                    [MAIN_DB_PREFIX . 'categorie_product.fk_categorie', $marca, '='],
                    [MAIN_DB_PREFIX . 'product.entity', $conf->entity, '='],
                ]);
            }
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

                    $stock = $this->cargarStockAlmacen($product->id);

                    $producto_array = [
                        'id' => $product->id,
                        'ref' => $product->ref,
                        'label' => $product->label,
                        'stock' => $stock,
                        'moneda' => $product->array_options["options_moneda"] ? [$product->array_options["options_moneda"] => $product->array_options["options_moneda"], "MXN" => "MXN"] : ["MXN" => "MXN", "USD" => "USD"],
                        'precio1' => round($product->multiprices[1], 2),
                        'precio2' => round($product->multiprices[2], 2),
                        'precio3' => round($product->multiprices[3], 2),

                    ];
                    array_push($productos, $producto_array);
                }

                $i++;

            }
        }

        return $productos;
    }

    public function cargarStockAlmacen($id)
    {
        global $db;

        $product = new Product($db);
        $product->fetch($id);
        $product->load_stock();
        $stock = [];
        asort($product->stock_warehouse);

        foreach ($product->stock_warehouse as $key => $value) {

            $stock_warehouse = [];

            $campos = array('ref');
            $consulta = PO\QueryBuilder::factorySelect();
            $consulta->select($campos);
            $consulta->from(MAIN_DB_PREFIX . 'entrepot');
            $consulta->where([
                ['rowid', $key, '='],

            ]);
            $resql = $db->query($consulta->toSql());
            $num = $db->num_rows($resql);
            if ($num) {

                $obj = $db->fetch_object($resql);
                $stock_warehouse = [

                    'warehouse' => $obj->ref,
                    'stock' => $value->real,

                ];

                array_push($stock, $stock_warehouse);
            }
        }

        return $stock;
    }

    public function cargarLoteProducto($producto, $almacen = null)
    {
        // echo $producto;
        // exit;
        global $db, $conf, $user;

        $product = new Product($db);
        $product->fetch($producto);

        if ($product->array_options["options_batch"] == 1) {
            $campos = [MAIN_DB_PREFIX . 'product_batch.rowid', 'batch', 'qty'];
            $select = new PO\QueryBuilder\Statements\Select();
            $select->select($campos);
            $select->from(MAIN_DB_PREFIX . 'product_batch');
            $select->innerJoin(MAIN_DB_PREFIX . 'product_stock', MAIN_DB_PREFIX . 'product_batch.fk_product_stock  = ' . MAIN_DB_PREFIX . 'product_stock.rowid');
            $select->where(MAIN_DB_PREFIX . 'product_stock.fk_product', $product->id, '=');
            if ($almacen == null) {
                $select->where(MAIN_DB_PREFIX . 'product_stock.fk_entrepot', $user->fk_warehouse, '=');
            } else {

                $select->where(MAIN_DB_PREFIX . 'product_stock.fk_entrepot', $almacen, '=');
                
            }
            $select->where(MAIN_DB_PREFIX . 'product_batch.qty', 0, '>');

            $resql = $db->query($select->toSql());
            $num = $db->num_rows($resql);
            $pedimentos = [];

            $totalStock = 0;
            $i = 0;
            if ($num) {
                while ($i < $num) {

                    $obj = $db->fetch_object($resql);
                    array_push($pedimentos, $obj);
                    $i++;
                }
            }

            return [
                'msg' => 'success',
                'data' => $pedimentos,
            ];

        }

    }

    public function cargarStockProducto($id)
    {
        global $db, $conf, $user;
        $campos = ['reel'];
        $select = new PO\QueryBuilder\Statements\Select();
        $select->select($campos);
        $select->from(MAIN_DB_PREFIX . 'product_stock');
        $select->where('fk_product', $id, '=');
        $select->where('fk_entrepot', $user->array_options["options_almacenpos"], '=');

        $resql = $db->query($select->toSql());

        $num = $db->num_rows($resql);

        if (!$num > 0) {
            return [
                'code' => 'err',
                'msg' => 'Sin Stock Disponible',
            ];
        } else {
            $respuesta = $db->fetch_object($resql);

            return [
                'code' => 'success',
                'msg' => $respuesta->reel,
            ];
        }

    }

}
