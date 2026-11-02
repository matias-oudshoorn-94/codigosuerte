<?php
// Login de sucursales. Puede recibir $msg (error) y $usuario_ingresado (para no volver a tipearlo).
$base_url = base_url()."application/views/layouts";
$lg_usuario = isset($usuario_ingresado) ? $usuario_ingresado : '';
$lg_error = isset($msg) && $msg !== '' ? $msg : '';
?>

<style type="text/css">
.lg {
    --lg-verde: #47b475;
    --lg-verde-oscuro: #3a9862;
    --lg-texto: #1f2933;
    --lg-texto-suave: #6b7785;
    --lg-borde: #d9dfe6;
    --lg-fondo: #f5f7f9;
    --lg-rojo: #c5202b;
    --lg-rojo-claro: #fdecec;
    position: relative;
    min-height: calc(100vh - 64px);
    display: flex; align-items: center; justify-content: center;
    padding: 40px 16px;
    color: var(--lg-texto);
    font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
    background: #1f2329 url("<?php echo $base_url ?>/img/home12.jpg") center / cover no-repeat;
}
.lg::before { content: ""; position: absolute; inset: 0; background: linear-gradient(160deg, rgba(31, 35, 41, .85), rgba(31, 35, 41, .6)); }
.lg * { box-sizing: border-box; }
.lg h1, .lg p, .lg label, .lg input, .lg button { margin: 0; letter-spacing: 0; text-transform: none; }

.lg-tarjeta { position: relative; width: 100%; max-width: 400px; background: #fff; border-radius: 16px; box-shadow: 0 24px 48px rgba(0, 0, 0, .28); padding: 32px 28px 28px; }
.lg-logo { display: block; height: 64px; width: auto; margin: 0 auto 20px; border-radius: 6px; }
.lg-tarjeta h1 { font-size: 22px; font-weight: 700; text-align: center; }
.lg .lg-sub { text-align: center; color: var(--lg-texto-suave); font-size: 14px; margin-top: 6px; margin-bottom: 24px; }

.lg-error { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 10px; background: var(--lg-rojo-claro); color: var(--lg-rojo); font-size: 14px; font-weight: 600; margin-bottom: 18px; animation: lg-sacudir .35s ease; }
.lg-error i { font-size: 16px; margin-top: 1px; }
@keyframes lg-sacudir { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-6px); } 75% { transform: translateX(6px); } }

.lg-campo { margin-bottom: 16px; }
.lg-campo label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
.lg-control { position: relative; }
.lg-control > i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9aa5b1; font-size: 15px; pointer-events: none; }
.lg .lg-control input { width: 100%; height: 48px; margin: 0; padding: 0 44px 0 42px; border: 1px solid var(--lg-borde); border-radius: 10px; background: #fff; font-size: 15px; color: var(--lg-texto); font-family: inherit; transition: border-color .12s ease, box-shadow .12s ease; }
.lg .lg-control input:focus { outline: none; border-color: var(--lg-verde); box-shadow: 0 0 0 3px rgba(71, 180, 117, .2); }
.lg .lg-control input.invalido { border-color: var(--lg-rojo); }
.lg .lg-control input.invalido:focus { box-shadow: 0 0 0 3px rgba(197, 32, 43, .15); }
.lg .lg-ver { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; padding: 0; border: 0; border-radius: 8px; background: none; color: var(--lg-texto-suave); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; }
.lg .lg-ver:hover { background: var(--lg-fondo); color: var(--lg-texto); }
.lg .lg-ver.activo { color: var(--lg-verde-oscuro); }
.lg-mayus { display: none; font-size: 12px; color: #b7790a; margin-top: 6px; }
.lg-mayus.visible { display: block; }

.lg .lg-enviar { width: 100%; height: 50px; line-height: 1; border: 0; border-radius: 10px !important; text-transform: none; letter-spacing: 0; background: var(--lg-verde); color: #fff; font-size: 16px; font-weight: 600; font-family: inherit; cursor: pointer; margin-top: 6px; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background .12s ease; }
.lg .lg-enviar:hover { background: var(--lg-verde-oscuro); }
.lg .lg-enviar:focus-visible { outline: 3px solid rgba(71, 180, 117, .45); outline-offset: 2px; }
.lg .lg-enviar:disabled { opacity: .7; cursor: default; }
.lg-girando { width: 16px; height: 16px; border: 2px solid rgba(255, 255, 255, .5); border-top-color: #fff; border-radius: 50%; animation: lg-girar .7s linear infinite; display: none; }
.lg-enviar.enviando .lg-girando { display: inline-block; }
@keyframes lg-girar { to { transform: rotate(360deg); } }

.lg .lg-ayuda { text-align: center; font-size: 13px; color: var(--lg-texto-suave); margin-top: 18px; }

@media (max-width: 480px) {
    .lg { padding: 20px 12px; align-items: flex-start; }
    .lg-tarjeta { padding: 26px 20px 22px; }
}
</style>

<section class="lg">
    <div class="lg-tarjeta">
        <img class="lg-logo" src="<?php echo $base_url ?>/img/logo2.png" alt="PDF Color">
        <h1>Ingresá a tu sucursal</h1>
        <p class="lg-sub">Usá el usuario y la contraseña de la sucursal</p>

        <?php if ($lg_error !== '') { ?>
            <div class="lg-error" role="alert"><i class="ti-alert"></i><span><?php echo htmlspecialchars($lg_error) ?></span></div>
        <?php } ?>

        <form id="lg-form" action="<?php echo base_url() ?>loginProc" method="post" novalidate>
            <div class="lg-campo">
                <label for="lg-usuario">Usuario</label>
                <div class="lg-control">
                    <i class="ti-user"></i>
                    <input type="text" name="usuario" id="lg-usuario" value="<?php echo htmlspecialchars($lg_usuario) ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required<?php echo $lg_error !== '' ? ' class="invalido"' : '' ?>>
                </div>
            </div>

            <div class="lg-campo">
                <label for="lg-clave">Contraseña</label>
                <div class="lg-control">
                    <i class="ti-lock"></i>
                    <input type="password" name="clave" id="lg-clave" autocomplete="current-password" required<?php echo $lg_error !== '' ? ' class="invalido"' : '' ?>>
                    <button type="button" class="lg-ver" id="lg-ver" aria-label="Mostrar contraseña" title="Mostrar contraseña"><i class="ti-eye"></i></button>
                </div>
                <div class="lg-mayus" id="lg-mayus"><i class="ti-info-alt"></i> Bloq Mayús está activado</div>
            </div>

            <button type="submit" class="lg-enviar" id="lg-enviar"><span class="lg-girando"></span><span>Ingresar</span></button>
        </form>

        <p class="lg-ayuda">¿No podés entrar? Pedile al administrador que revise tu usuario.</p>
    </div>
</section>

<script type="text/javascript">
(function () {
    var form = document.getElementById('lg-form');
    var usuario = document.getElementById('lg-usuario');
    var clave = document.getElementById('lg-clave');
    var ver = document.getElementById('lg-ver');
    var enviar = document.getElementById('lg-enviar');
    var mayus = document.getElementById('lg-mayus');

    // Foco: si el usuario ya viene cargado (despues de un error), ir a la contraseña
    (usuario.value ? clave : usuario).focus();

    ver.addEventListener('click', function () {
        var mostrar = clave.type === 'password';
        clave.type = mostrar ? 'text' : 'password';
        ver.classList.toggle('activo', mostrar);
        ver.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
        ver.title = ver.getAttribute('aria-label');
        clave.focus();
    });

    clave.addEventListener('keyup', function (e) {
        if (e.getModifierState) mayus.classList.toggle('visible', e.getModifierState('CapsLock'));
    });

    [usuario, clave].forEach(function (campo) {
        campo.addEventListener('input', function () { campo.classList.remove('invalido'); });
    });

    form.addEventListener('submit', function (e) {
        var faltan = [usuario, clave].filter(function (campo) { return campo.value.trim() === ''; });
        if (faltan.length) {
            e.preventDefault();
            faltan.forEach(function (campo) { campo.classList.add('invalido'); });
            faltan[0].focus();
            return;
        }
        enviar.disabled = true;
        enviar.classList.add('enviando');
    });
})();
</script>
