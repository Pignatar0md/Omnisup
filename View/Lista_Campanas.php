<br>
<br>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Omnisup/config.php';
include controllers . '/Campana.php';

$Controller_Campana = new Campana();
$resul = $Controller_Campana->traerCampanas();
?>
<div class="col-md-3 col-lg-offset-4">
    <table id="tableCamp" class="table table-striped table-condensed">
        <thead>
            <tr><th>Campañas</th></tr>
        </thead>
        <tbody>
            <?php
            foreach ($resul as $clave => $valor) {
                foreach ($valor as $cla => $val) {
                    ?>   
                    <tr>
                        <td style='color:green'><a href="index.php?page=Detalle_Campana&nomcamp=<?= $val ?>"><?= $val ?></a></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>