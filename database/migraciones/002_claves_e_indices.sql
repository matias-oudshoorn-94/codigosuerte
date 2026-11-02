-- Migracion 002: claves primarias faltantes e indices.
--
-- 1) Las tablas de usuarios del admin (users, groups, users_groups) no tenian
--    clave primaria ni autoincremento: los usuarios creados despues del admin
--    quedaron todos con id = 0. Se les asigna un id propio (no se borra nada).
-- 2) Indices para las consultas mas frecuentes (pedidos por sucursal y fecha,
--    detalle por pedido, stock por producto y sucursal, carrito por sucursal).
--
-- Es seguro correrla una sola vez. Conviene hacer un backup antes.

-- ---------------------------------------------------------------
-- 1) users: asignar ids a los que tienen id = 0
-- ---------------------------------------------------------------
SET @siguiente := (SELECT COALESCE(MAX(id), 0) FROM users);
UPDATE users SET id = (@siguiente := @siguiente + 1) WHERE id = 0 ORDER BY created_on;

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  ADD UNIQUE KEY `uc_users_groups` (`user_id`, `group_id`);

-- ---------------------------------------------------------------
-- 2) Indices
-- ---------------------------------------------------------------
ALTER TABLE `pedidos`
  ADD KEY `idx_cliente_fecha` (`cod_clie`, `fecha`);

ALTER TABLE `pedidosdetalles`
  ADD KEY `idx_pedido` (`idpedido`);

ALTER TABLE `stock_sucursal`
  ADD UNIQUE KEY `uq_producto_sucursal` (`producto_id`, `idusuario`);

ALTER TABLE `shopping`
  DROP KEY `id`,                       -- duplicaba la clave primaria
  ADD KEY `idx_cliente` (`cod_clie`);

ALTER TABLE `productos`
  ADD KEY `idx_codigo` (`codigo`);

ALTER TABLE `detalles_impresoras`
  ADD KEY `idx_impresora` (`id_impresora`);

ALTER TABLE `usuarios`
  ADD KEY `idx_usuario` (`usuario`);
