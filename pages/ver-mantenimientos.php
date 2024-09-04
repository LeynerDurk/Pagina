<?php
require('../config/database.php');
include_once('../layout/header.php');

$id = $_GET['id'];

$query = "SELECT e.id, e.codigo, e.tipo, e.idmarca, e.idsala, e.fechaingreso, m.nombremarca, s.nombresala, ma.fechafin FROM equipos e LEFT OUTER JOIN marcas m ON e.idmarca = m.id LEFT OUTER JOIN salas s ON e.idsala = s.id LEFT OUTER JOIN mantenimientos ma ON e.id = ma.idequipo WHERE e.id = '$id'";
$equipos = $dbConnection->query($query);

$fechault = "";
$fechaingreso = "";
?>

<main class="container flex gap-5 my-16">

    <table class="table table-hover table-dark">
        <thead>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Código
                </th>
                <th>
                    Tipo
                </th>
                <th>
                    Marca
                </th>
                <th>
                    Sala
                </th>
                <th>
                    Fecha Ingreso
                </th>
                <th>
                    Fecha Último Mantenimiento
                </th>
                <th>
                    Fecha Siguiente Mantenimiento
                </th>           
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $equipos->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['codigo']; ?></td>
                    <td><?php echo $row['tipo']; ?></td>
                    <td><?php echo $row['nombremarca']; ?></td>
                    <td><?php echo $row['nombresala']; ?></td>
                    <td><?php echo $row['fechaingreso']; ?></td>
                    <td><?php echo $row['fechafin']; ?></td>
                    <td>
                        <?php
                        if ($fechault !== "") {
                            $fechasigui = new DateTime($fechault);
                            $fechasigui->add(new DateInterval('P180D'));
                            echo $fechasigui->format('Y-m-d');
                        } else {
                            $fechasigui = new DateTime($fechaingreso);
                            $fechasigui->add(new DateInterval('P180D'));
                            echo $fechasigui->format('Y-m-d');
                        }
                        ?>

                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</main>


<?php include_once('../layout/footer.php'); ?>