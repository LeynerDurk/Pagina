<?php
require('../config/database.php');
include_once('../layout/header.php');


$query = "SELECT * FROM monitores";
$monitores = $dbConnection->query($query);

$id = 0;
$nombremonitor = "";
$errores = array();

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    $id = $_GET['id'] ?? 0;

    if (isset($_GET['edit'])) {
        $query = "SELECT * FROM monitores WHERE id = $id";
        $result = $dbConnection->query($query);
        $monitor = $result->fetch_array();
        $nombremonitor = $monitor["nombremonitor"];
    } else {
        $query = "DELETE FROM monitores WHERE id = $id";
        $result = $dbConnection->query($query);

        if ($dbConnection->affected_rows > 0) {
            if ($result === TRUE) {
                header('Location: ./monitores.php');
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $id = $_GET['id'] ?? 0;
    $nombremonitor = $_POST['nombremonitor'];

    if ($nombremonitor === "") {
        array_push($errores, 'El Nombre del Monitor Es Requerido');
    } else {

        $query = "SELECT * FROM monitores m WHERE m.nombremonitor = LOWER('$nombremonitor')";
        $existe = $dbConnection->query($query);

        if ($existe->num_rows === 0) {
            if ($id === 0) {
                $query = "INSERT INTO monitores(nombremonitor) VALUES ('$nombremonitor')";
            } else {
                $query = "UPDATE monitores SET nombremonitor = '$nombremonitor' WHERE id = '$id'";
            }
            $dbConnection->query($query);
            header('Location: ./monitores.php');
        } else {
            array_push($errores, 'Ya se encuentra registrado el nombre de monitor');
        }
    }
}

?>

<main class="container flex gap-5 mt-5">

    <form method="POST" class="flex flex-col gap-5 w-2/4">

        <h1 class="font-bold text-2xl text-center">Gestión de Monitores</h1>

        <?php
        if (!empty($errores)) {
            echo '<div class="bg-red-500 text-white font-bold p-1 rounded text-center capitalize">
            <p>' . $errores[0] . '</p>
            </div>';
        }
        ?>


        <div class="flex flex-col">
            <label for="nombremonitor" class="font-bold">Nombre</label>
            <input type="text" name="nombremonitor" id="nombremonitor" placeholder="Ingresa el Nombre de monitor" class="p-1 rounded border" value="<?php echo $nombremonitor ?>">
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
            while ($row = $monitores->fetch_assoc()) {
            ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombremonitor']; ?></td>
                    <td>
                        <a href='./monitores.php?id=<?php echo $row['id']; ?>&edit=true' class="bg-indigo-500 p-1 rounded font-bold text-white hover:bg-indigo-600 hover:cursor-pointer">Editar</a>
                        <a href='./monitores.php?id=<?php echo $row['id']; ?>' class="bg-red-500 p-1 rounded font-bold text-white hover:bg-red-600 hover:cursor-pointer">Eliminar</a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</main>


<?php include_once('../layout/footer.php'); ?>