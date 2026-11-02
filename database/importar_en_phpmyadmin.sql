-- =====================================================================
-- Actualizacion de la base para la version CodeIgniter 3 / PHP 8
-- Importar en phpMyAdmin con la base u245081402_codigosuerte seleccionada.
--
-- IMPORTANTE:
--  * Hacer un backup (Exportar) antes de importar.
--  * Importarlo UNA sola vez (si se repite, da error de clave duplicada).
--  * Importarlo justo despues de subir el codigo nuevo: el codigo viejo
--    no funciona con la tabla de sesiones nueva. Todos los usuarios
--    tendran que volver a iniciar sesion una vez.
-- =====================================================================

DROP TABLE IF EXISTS `ci_sessions`;

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
