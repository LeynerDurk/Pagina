<?php
require('../config/database.php');
include_once('../layout/header.php');


$query = "SELECT * FROM sedes";
$sedes = $dbConnection->query($query);

$id = 0;
$nombresede = "";
$errores = array();

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $id = $_GET['id'] ?? 0;

    if (isset($_GET['edit'])) {
        $query = "SELECT * FROM sedes WHERE id = $id";
        $result = $dbConnection->query($query);
        $sede = $result->fetch_array();
        $nombresede = $sede["nombresede"];
    } else {
        $query = "DELETE FROM sedes WHERE id = $id";
        $result = $dbConnection->query($query);

        if ($dbConnection->affected_rows > 0) {
            if ($result === TRUE) {
                header('Location: ./sedes.php');
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $id = $_GET['id'] ?? 0;
    $nombresede = $_POST['nombresede'];

    if ($nombresede === "") {
        array_push($errores, 'El Nombre de sede Es Requerido');
    } else {

        $query = "SELECT * FROM sedes m WHERE m.nombresede = LOWER('$nombresede')";
        $existe = $dbConnection->query($query);

        if ($existe->num_rows === 0) {
            if ($id === 0) {
                $query = "INSERT INTO sedes(nombresede) VALUES ('$nombresede')";
            } else {
                $query = "UPDATE sedes SET nombresede = '$nombresede' WHERE id = '$id'";
            }
            $dbConnection->query($query);
            header('Location: ./sedes.php');
        } else {
            array_push($errores, 'Ya se encuentra registrado el nombre de sede');
        }
    }
}

?>

<main class="container flex gap-5 mt-5">

    <form method="POST" class="flex flex-col gap-5 w-2/4">

        <h1 class="font-bold text-2xl text-center">Gestión de Sedes</h1>

        <?php
        if (!empty($errores)) {
            echo '<div class="bg-red-500 text-white font-bold p-1 rounded text-center capitalize">
            <p>' . $errores[0] . '</p>
            </div>';
        }
        ?>


        <div class="flex flex-col">
            <label for="nombresede" class="font-bold">Nombre</label>
            <input type="text" name="nombresede" id="nombresede" placeholder="Ingresa el Nombre de sede" class="p-1 rounded border" value="<?php echo $nombresede ?>">
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
                    Acciones
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $sedes->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombresede']; ?></td>
                    <td>
                        <a href='./sedes.php?id=<?php echo $row['id']; ?>&edit=true' class="bg-indigo-500 p-1 rounded font-bold text-white hover:bg-indigo-600 hover:cursor-pointer">Editar</a>
                        <a href='./sedes.php?id=<?php echo $row['id']; ?>' class="bg-red-500 p-1 rounded font-bold text-white hover:bg-red-600 hover:cursor-pointer">Eliminar</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</main>


<?php include_once('../layout/footer.php'); ?>