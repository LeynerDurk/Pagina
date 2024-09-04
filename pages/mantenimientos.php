<?php
require('../config/database.php');
include_once('../layout/header.php');

$queryEquipos = "SELECT * FROM equipos e WHERE e.estado = 1";
$equipos = $dbConnection->query($queryEquipos);

$queryMonitores = "SELECT * FROM monitores";
$monitores = $dbConnection->query($queryMonitores);

$query = "SELECT m.id, m.idequipo, m.tipomantenimiento, m.problema, m.fechainicio, m.idmonitor, m.fechafin, m.descripcion, e.codigo, mk.nombremarca, sa.nombresala, se.nombresede, p.nombremonitor FROM mantenimientos m LEFT OUTER JOIN equipos e ON m.idequipo = e.id LEFT OUTER JOIN marcas mk ON e.idmarca = mk.id LEFT OUTER JOIN salas sa ON e.idsala = sa.id LEFT OUTER JOIN sedes se ON sa.idsede = se.id LEFT OUTER JOIN monitores p ON m.idmonitor = p.id";
$mantenimientos = $dbConnection->query($query);

$id = 0;
$codigo = "";
$idequipo = "";
$tipomantenimiento = "";
$fechainicio = "";
$problema = "";
$idmonitor = "";
$fechafin = "";
$descripcion = "";
$errores = array();

$edit = $_GET['edit'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $id = $_GET['id'] ?? 0;

    if (isset($_GET['edit'])) {
        $query = "SELECT m.id, m.idequipo, m.tipomantenimiento, m.problema, m.fechainicio, m.idmonitor, m.fechafin, m.descripcion, e.codigo FROM mantenimientos m LEFT OUTER JOIN equipos e ON m.idequipo = e.id WHERE m.id = $id";
        $result = $dbConnection->query($query);
        $mantenimiento = $result->fetch_array();

        $idequipo = $mantenimiento['idequipo'];
        $tipomantenimiento = $mantenimiento['tipomantenimiento'];
        $fechainicio = $mantenimiento['fechainicio'];
        $problema = $mantenimiento['problema'];
        $idmonitor = $mantenimiento['idmonitor'];
        $codigo = $mantenimiento['codigo'];
    } else {
        $query = "DELETE FROM mantenimientos WHERE id = $id";
        $result = $dbConnection->query($query);

        if ($dbConnection->affected_rows > 0) {
            if ($result === TRUE) {
                header('Location: ./mantenimientos.php');
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $id = $_GET['id'] ?? 0;
    $idequipo = $_POST['idequipo'];
    $tipomantenimiento = $_POST['tipomantenimiento'];
    $fechainicio = $_POST['fechainicio'];
    $problema = $_POST['problema'];
    $idmonitor = $_POST['idmonitor'];
    $fechafin = $_POST['fechafin'];
    $descripcion = $_POST['descripcion'];

    if ($idequipo === "") {
        array_push($errores, 'Seleccione el equipo');
    }

    if ($tipomantenimiento === "") {
        array_push($errores, 'El tipo de mantenimiento es Requerido');
    }

    if ($idmonitor === "") {
        array_push($errores, 'Debes seleccionar un monitor');
    }

    if ($fechainicio === "") {
        array_push($errores, 'Debes agregar una fecha de inicio');
    }

    if (empty($errores)) {
        if ($id === 0) {
            $query = "INSERT INTO mantenimientos(idequipo, tipomantenimiento, problema, fechainicio, idmonitor) VALUES ('$idequipo', '$tipomantenimiento','$problema','$fechainicio','$idmonitor')";
            $queryUpdate = "UPDATE equipos SET estado = 0 WHERE id = '$idequipo'";
            $dbConnection->query($queryUpdate);
        } else {            
            $query = "UPDATE mantenimientos SET tipomantenimiento = '$tipomantenimiento', problema = '$problema', fechainicio = '$fechainicio', idmonitor = '$idmonitor', descripcion = '$descripcion' WHERE id = '$id'";
            if ($fechafin !== "") {
                $query = "UPDATE mantenimientos SET fechafin = '$fechafin' WHERE id = '$id'";
                $dbConnection->query($query);
                $queryUpdate = "UPDATE equipos SET estado = 1 WHERE id = '$idequipo'";
                $dbConnection->query($queryUpdate);
            }
        }
        $dbConnection->query($query);
        header('Location: ./mantenimientos.php');
    }
}

?>

<main class=" flex gap-5 mt-5">

    <form method="POST" class="flex flex-col gap-5 ">

        <h1 class="font-bold text-2xl text-center">Gestión de Mantenimientos</h1>

        <?php
        if (!empty($errores)) {
            for ($i = 0; $i < count($errores); $i++) {
                echo '<div class="bg-red-500 text-white font-bold p-1 rounded text-center capitalize">
            <p>' . $errores[$i] . '</p>
            </div>';
            }
        }
        ?>

        <div class="flex flex-col">
            <label for="idequipo" class="font-bold">Equipo</label>
            <?php if ($edit) { ?>
                <input type="text" value="<?php echo $codigo; ?>" disabled>
                <input type="hidden" value="<?php echo $idequipo ?>" name="idequipo">
            <?php } else { ?>
                <select name="idequipo" id="idequipo">
                    <option value="">-- Seleccione --</option>
                    <?php while ($row = $equipos->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo $codigo === $row['codigo'] ? 'selected' : ''; ?>><?php echo $row['codigo']; ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>

        <div class="flex flex-col">
            <label for="tipomantenimiento" class="font-bold">Tipo Mantenimiento</label>
            <select name="tipomantenimiento" id="tipomantenimiento">
                <option value="">-- Seleccione --</option>
                <option value="1" <?php echo $tipomantenimiento == "1" ? 'selected' : ''; ?>>Preventivo</option>
                <option value="2" <?php echo $tipomantenimiento == "2" ? 'selected' : ''; ?>>Correctivo</option>
            </select>
        </div>

        <div class="flex flex-col">
            <label for="fechainicio" class="font-bold">Fecha Inicio</label>
            <input type="date" name="fechainicio" id="fechainicio" value="<?php echo $fechainicio; ?>">
        </div>

        <div class="flex flex-col">
            <label for="problema" class="font-bold">Descripción del Problema</label>
            <textarea name="problema" id="problema"><?php echo $problema; ?></textarea>
        </div>

        <div class="flex flex-col">
            <label for="idmonitor" class="font-bold">Monitores</label>
            <select name="idmonitor" id="idmonitor">
                <option value="">-- Seleccione --</option>
                <?php while ($row = $monitores->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo $idmonitor === $row['id'] ? 'selected' : '' ?>><?php echo $row['nombremonitor']; ?></option>
                <?php } ?>
            </select>
        </div>

        <?php if ($edit) { ?>
            <div class="flex flex-col">
                <label for="fechafin" class="font-bold">Fecha Terminación</label>
                <input type="date" name="fechafin" id="fechafin" value="<?php echo $fechafin; ?>">
            </div>

            <div class="flex flex-col">
                <label for="descripcion" class="font-bold">Descripción del Trabajo</label>
                <textarea name="descripcion" id="descripcion"><?php echo $descripcion; ?></textarea>
            </div>
        <?php } ?>

        <input type="submit" value="Guardar" class="p-1 rounded-md text-white font-bold uppercase bg-indigo-500 hover:bg-indigo-600 hover:cursor-pointer">
    </form>

    <table class="table table-hover table-dark">
        <thead>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Código Equipo
                </th>
                <th>
                    Marca
                </th>
                <th>
                    Sala
                </th>
                <th>
                    Sede
                </th>
                <th>
                    Tipo Mantenimiento
                </th>
                <th>
                    Problema
                </th>
                <th>
                    Fecha Inicio
                </th>
                <th>
                    Monitor
                </th>
                <th>
                    Fecha Fin
                </th>
                <th>
                    Descripción
                </th>
                <th>
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $mantenimientos->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['codigo']; ?></td>
                    <td><?php echo $row['nombremarca']; ?></td>
                    <td><?php echo $row['nombresala']; ?></td>
                    <td><?php echo $row['nombresede']; ?></td>
                    <td><?php echo $row['tipomantenimiento'] === 1 ? 'Preventivo' : 'Correctivo'; ?></td>
                    <td><?php echo $row['problema']; ?></td>
                    <td><?php echo $row['fechainicio']; ?></td>
                    <td><?php echo $row['nombremonitor']; ?></td>
                    <td><?php echo $row['fechafin']; ?></td>
                    <td><?php echo $row['descripcion']; ?></td>
                    <td>
                        <a href='./mantenimientos.php?id=<?php echo $row['id']; ?>&edit=true' class="bg-indigo-500 p-1 rounded font-bold text-white hover:bg-indigo-600 hover:cursor-pointer">Editar</a>
                        <a href='./mantenimientos.php?id=<?php echo $row['id']; ?>' class="bg-red-500 p-1 rounded font-bold text-white hover:bg-red-600 hover:cursor-pointer">Eliminar</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</main>


<?php include_once('../layout/footer.php'); ?>