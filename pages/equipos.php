<?php
require('../config/database.php');
include_once('../layout/header.php');

$queryMarcas = "SELECT * FROM marcas";
$marcas = $dbConnection->query($queryMarcas);

$querySalas = "SELECT * FROM salas";
$salas = $dbConnection->query($querySalas);

$query = "SELECT e.id, e.codigo, e.tipo, e.idmarca, e.idsala, e.fechaingreso, e.estado, m.nombremarca, s.nombresala, MAX(ma.fechafin) as fechafin FROM equipos e LEFT OUTER JOIN marcas m ON e.idmarca = m.id LEFT OUTER JOIN salas s ON e.idsala = s.id LEFT OUTER JOIN mantenimientos ma ON e.id = ma.idequipo GROUP BY e.id;";
$equipos = $dbConnection->query($query);

$id = 0;
$codigo = "";
$tipo = "";
$idmarca = 0;
$fechaingreso = "";
$idsala = 0;
$estado = null;
$fechault = "";
$fechasigui = "";
$errores = array();

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $id = $_GET['id'] ?? 0;

    if (isset($_GET['edit'])) {
        $query = "SELECT * FROM equipos WHERE id = $id";
        $result = $dbConnection->query($query);
        $equipo = $result->fetch_array();
        $codigo = $equipo["codigo"];
        $tipo = $equipo["tipo"];
        $idmarca = $equipo["idmarca"];
        $fechaingreso = $equipo['fechaingreso'];
        $idsala = $equipo["idsala"];
        $estado = $equipo["estado"];
    } else {
        $query = "DELETE FROM equipos WHERE id = $id";
        $result = $dbConnection->query($query);

        if ($dbConnection->affected_rows > 0) {
            if ($result === TRUE) {
                header('Location: ./equipos.php');
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $id = $_GET['id'] ?? 0;
    $codigo = $_POST['codigo'];
    $tipo = $_POST['tipo'];
    $idmarca = $_POST['idmarca'];
    $fechaingreso = $_POST['fechaingreso'];
    $idsala = $_POST['idsala'];
    $estado = isset($_POST['estado']);

    if ($codigo === "") {
        array_push($errores, 'El Nombre de equipo Es Requerido');
    }

    if ($tipo === "") {
        array_push($errores, 'El tipo de equipo Es Requerido');
    }

    if ($idmarca === "") {
        array_push($errores, 'Debes seleccionar una marca');
    }

    if ($fechaingreso === "") {
        array_push($errores, 'Debes agregar una fecha de ingreso');
    }

    if ($idsala === "") {
        array_push($errores, 'Debes seleccionar una sala');
    }

    if (empty($errores)) {

        $estado = $estado ? 1 : 0;

        $query = "SELECT * FROM equipos m WHERE m.codigo = LOWER('$codigo')";
        $existe = $dbConnection->query($query);

        if ($existe->num_rows === 0) {
            if ($id === 0) {
                $query = "INSERT INTO equipos(codigo, tipo, idmarca, fechaingreso, idsala, estado) VALUES ('$codigo', '$tipo','$idmarca','$fechaingreso','$idsala', '$estado')";
            } else {
                $query = "UPDATE equipos SET codigo = '$codigo', tipo = '$tipo', idmarca = '$idmarca', fechaingreso = '$fechaingreso', idsala = '$idsala', estado = '$estado' WHERE id = '$id'";
            }
            $dbConnection->query($query);
            header('Location: ./equipos.php');
        } else {
            array_push($errores, 'Ya se encuentra registrado el nombre de equipo');
        }
    }
}

?>

<main class="container flex gap-5 my-16">

    <form method="POST" class="flex flex-col ">

        <h1 class="font-bold text-2xl mb-7 text-center">Gestión de Equipos</h1>

        <?php
        if (!empty($errores)) {
            for ($i = 0; $i < count($errores); $i++) {
                echo '<div class="bg-red-500 text-white font-bold p-1 rounded text-center capitalize">
            <p>' . $errores[$i] . '</p>
            </div>';
            }
        }
        ?>

        <div class="flex flex-col mt-2">
            <label for="codigo" class="font-bold">Código del Equipo</label>
            <input type="text" name="codigo" id="codigo" placeholder="Ingresa el Nombre de equipo" class="p-1 rounded border" value="<?php echo $codigo ?>">
        </div>

        <div class="flex flex-col mt-2">
            <label for="tipo" class="font-bold">Tipo</label>
            <select name="tipo" id="tipo">
                <option value="">-- Seleccione --</option>
                <option value="Portatil" <?php echo $tipo === "Portatil" ? 'selected' : '' ?>>Portatil</option>
                <option value="PC" <?php echo $tipo === "PC" ? 'selected' : '' ?>>PC</option>
            </select>
        </div>

        <div class="flex flex-col mt-2">
            <label for="idmarca" class="font-bold">Marca</label>
            <select name="idmarca" id="idmarca">
                <option value="">-- Seleccione --</option>
                <?php while ($row = $marcas->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo $idmarca === $row['id'] ? 'selected' : '' ?>><?php echo $row['nombremarca']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="flex flex-col mt-2">
            <label for="fechaingreso" class="font-bold">Fecha Ingreso</label>
            <input type="date" name="fechaingreso" id="fechaingreso" value="<?php echo $fechaingreso; ?>">
        </div>

        <div class="flex flex-col mt-2">
            <label for="idsala" class="font-bold">Sala</label>
            <select name="idsala" id="idsala">
                <option value="">-- Seleccione --</option>
                <?php while ($row = $salas->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo $idsala === $row['id'] ? 'selected' : '' ?>><?php echo $row['nombresala']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="flex gap-5 mt-2">
            <label for="estado" class="font-bold">Estado</label>
            <input type="radio" name="estado" id="estado">
        </div>

        <input type="submit" value="Guardar" class="p-1 rounded-md text-white font-bold uppercase bg-indigo-500 hover:bg-indigo-600 hover:cursor-pointer">
    </form>

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
                <th>
                    Estado
                </th>
                <th>
                    Acciones
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
                        $fechault = $row['fechafin'];
                        if ($fechault !== NULL) {
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
                    <td><?php echo $row['estado'] ? 'Funcionamiento' : 'Mantenimiento'; ?></td>
                    <td>
                        <a href='./equipos.php?id=<?php echo $row['id']; ?>&edit=true' class="bg-indigo-500 p-1 rounded font-bold text-white hover:bg-indigo-600 hover:cursor-pointer">Editar</a>
                        <a href='./equipos.php?id=<?php echo $row['id']; ?>' class="bg-red-500 p-1 rounded font-bold text-white hover:bg-red-600 hover:cursor-pointer">Eliminar</a>
                        <a href='./ver-mantenimientos.php?id=<?php echo $row['id']; ?>' class="bg-indigo-500 p-1 rounded font-bold text-white hover:bg-indigo-600 hover:cursor-pointer">Ver</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</main>


<?php include_once('../layout/footer.php'); ?>