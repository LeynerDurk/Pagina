<?php
require('../config/database.php');
include_once('../layout/header.php');

$query = "SELECT s.id AS id, s.nombresala, s.idsede, ss.nombresede FROM salas s LEFT OUTER JOIN sedes ss ON s.idsede = ss.id";
$salas = $dbConnection->query($query);

$querySedes = "SELECT * FROM sedes";
$sedes = $dbConnection->query($querySedes);

$id = 0;
$nombresala = "";
$idsede = 0;
$errores = array();

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $id = $_GET['id'] ?? 0;

    if (isset($_GET['edit'])) {
        $query = "SELECT * FROM salas WHERE id = $id";
        $result = $dbConnection->query($query);
        $sala = $result->fetch_array();
        $nombresala = $sala["nombresala"];
        $idsede = $sala["idsede"];
    } else {
        $query = "DELETE FROM salas WHERE id = $id";
        $result = $dbConnection->query($query);

        if ($dbConnection->affected_rows > 0) {
            if ($result === TRUE) {
                header('Location: ./salas.php');
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $id = $_GET['id'] ?? 0;
    $nombresala = $_POST['nombresala'];
    $idsede = $_POST['idsede'];

    if ($nombresala === "") {
        array_push($errores, 'El Nombre de sala Es Requerido');
    } else if ($idsede === 0) {
        array_push($errores, 'Las sede es requerida');
    } else {

        $query = "SELECT * FROM salas m WHERE m.nombresala = LOWER('$nombresala')";
        $existe = $dbConnection->query($query);

        if ($existe->num_rows === 0) {
            if ($id === 0) {
                $query = "INSERT INTO salas(nombresala, idsede) VALUES ('$nombresala', '$idsede')";
            } else {
                $query = "UPDATE salas SET nombresala = '$nombresala', idsede = '$idsede' WHERE id = '$id'";
            }
            $dbConnection->query($query);
            header('Location: ./salas.php');
        } else {
            array_push($errores, 'Ya se encuentra registrado el nombre de sala');
        }
    }
}

?>

<main class="container flex gap-5 mt-5">

    <form method="POST" class="flex flex-col gap-5 w-2/4">

        <h1 class="font-bold text-2xl text-center">Gestión de Salas</h1>

        <?php
        if (!empty($errores)) {
            echo '<div class="bg-red-500 text-white font-bold p-1 rounded text-center capitalize">
            <p>' . $errores[0] . '</p>
            </div>';
        }
        ?>


        <div class="flex flex-col">
            <label for="nombresala" class="font-bold">Nombre</label>
            <input type="text" name="nombresala" id="nombresala" placeholder="Ingresa el Nombre de sala" class="p-1 rounded border" value="<?php echo $nombresala ?>">
        </div>

        <div class="flex flex-col">
            <label for="nombresala" class="font-bold">Sede</label>
            <select name="idsede" id="idsede">
                <option value="">-- Seleccione --</option>
                <?php while ($row = $sedes->fetch_assoc()) { ?>
                    <option value="<?php echo $row['id'] ?>" <?php echo $idsede === $row['id'] ? 'selected' : '' ?>><?php echo $row['nombresede']; ?></option>
                <?php } ?>
            </select>
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
                    Nombre
                </th>
                <th>
                    Sede
                </th>
                <th>
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $salas->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombresala']; ?></td>
                    <td><?php echo $row['nombresede']; ?></td>
                    <td>
                        <a href='./salas.php?id=<?php echo $row['id']; ?>&edit=true' class="bg-indigo-500 p-1 rounded font-bold text-white hover:bg-indigo-600 hover:cursor-pointer">Editar</a>
                        <a href='./salas.php?id=<?php echo $row['id']; ?>' class="bg-red-500 p-1 rounded font-bold text-white hover:bg-red-600 hover:cursor-pointer">Eliminar</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</main>


<?php include_once('../layout/footer.php'); ?>