<?php
session_start();
require '../View/cortina.php';
require '../View/Header.php';
require '../Controller/conexion.php';
$db = new Database();
$conexion = $db->conectar();
if ($conexion === null) 
    {
        die("Error de la conexión a la base de datos");
    }
if (isset($_GET['id'])) 
    {
    $userId = $_GET['id'];
    if ($_SERVER["REQUEST_METHOD"] === "POST") 
        {
        $nuevaPassword = $_POST['Password'];
        $confirmarPassword = $_POST['ConfirmarPassword'];
        try 
            {
                // Verificar si el token_request está en 1
                $queryCheckToken = 'SELECT token_request FROM registro WHERE ID = :id';
                $stmtCheckToken = $conexion->prepare($queryCheckToken);
                $stmtCheckToken->bindParam(':id', $userId, PDO::PARAM_INT);
                $stmtCheckToken->execute();
                $tokenData = $stmtCheckToken->fetch(PDO::FETCH_ASSOC);
                if ($tokenData && $tokenData['token_request'] == 1) 
                    {
                        if ($nuevaPassword === $confirmarPassword) 
                            {
                                // Actualizar la contraseña y restablecer los tokens
                                $updateQuery = 'UPDATE registro SET Contrasenia = :contrasenia, token_password = NULL, token_request = 0 WHERE ID = :id';
                                $stmtUpdate = $conexion->prepare($updateQuery);
                                $stmtUpdate->bindParam(':contrasenia', $nuevaPassword, PDO::PARAM_STR);
                                $stmtUpdate->bindParam(':id', $userId, PDO::PARAM_INT);
                                $stmtUpdate->execute();
                                $a = 'SELECT FROM registro WHERE Num_Doc = :Num_Doc';

                                echo "<script>
                                       alert('Contraseña cambiada exitosamente');
                                       window.location.href = '../View/LoginRegister.php';
                                   </script>";
                            } 
                        else 
                            {
                                echo "<script>alert('Las contraseñas no coinciden');</script>";
                            }
                    } 
                else 
                    {
                        echo "<script>alert('Token de recuperación no válido o expirado');</script>";
                    }
            } 
        catch (PDOException $e) 
            {
                echo "<script>alert('Error en la base de datos: " . $e->getMessage() . "');</script>";
            }
        }
    } 
else 
    {
        echo "<script>alert('ID de usuario no válido');</script>";
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link rel="stylesheet" href="../Model/Css/Style.css">
    <style>
        .contenedor_todo {
            background-color: white;
            width: 380px;
            height: 450px;
            border-radius: 15px;
            text-align: center;
            margin: 10px;
        }
        .contenedor_todo form input {
            width: 80%;
            margin-top: 20px;
            padding: 10px;
            border: none;
            top: 45%;
            background: #f2f2f2;
            font-size: 16px;
            outline: none;
        }
        .contenedor_todo form button {
            padding: 10px 40px;
            margin-top: 40px;
            border: none;
            font-size: 14px;
            background: #46A2FD;
            color: white;
            cursor: pointer;
            outline: none;
        }
        .contenedor_todo form h1 {
            font-size: 30px;
            text-align: center;
            margin-bottom: 20px;
            color: #46A2FD;
        }
    </style>
</head>
<body>
    <center>
    <div class="contenedor_todo">
        <form action="CambioDeContraseña.php?id=<?php echo htmlspecialchars($userId); ?>" method="POST">
            <h1>Cambiar Contraseña</h1>
            <div class="pw">
               <input type="password" name="Password" id="contrasena" placeholder="Contraseña" required autocomplete="off">
               <img id="imagenOjo" src="../Images/OjoCerrado.jpeg" height="40px" width="3%" 
                style="position: absolute; top: 21%;  left:53%;transform: translateY(-50%); cursor: pointer;     background: #f2f2f2;"
                onmousedown="mostrarContrasena()" 
                onmouseup="ocultarContrasena()">
            </div>
            <br>
            <div class="pw1">
               <input type="password" name="ConfirmarPassword" id="contrasena1" placeholder="Confirmar Contraseña" required autocomplete="off">
               <img id="imagenOjo1" src="../Images/OjoCerrado.jpeg" height="40px" width="5px" 
                style="position: absolute; top: 29%; left:53%;transform: translateY(-50%); cursor: pointer;    background: #f2f2f2;"
                onmousedown="mostrarContrasena1()" 
                onmouseup="ocultarContrasena1()">
            </div>
            <br>
            <button type="submit">Aceptar</button>
        </form>
    </div>
    </center>
</body>
<footer>
<?php
    require '../View/Footer.php';
?>
</footer>
</html>
<script>
    let contrasenaInput  = document.getElementById("contrasena");
    let contrasenaInput1 = document.getElementById("contrasena1");
    let imagenOjo        = document.getElementById("imagenOjo");
    let imagenOjo1       = document.getElementById("imagenOjo1");
function mostrarContrasena() 
    {   contrasenaInput.type = "text";
        imagenOjo.src = "../Images/OjoAbierto.jpeg"; // Cambia la imagen al presionar
    }
function ocultarContrasena() 
    {   contrasenaInput.type = "password";
        imagenOjo.src = "../Images/OjoCerrado.jpeg"; // Cambia la imagen al soltar
    }
function mostrarContrasena1() 
    {   contrasenaInput1.type = "text";
        imagenOjo1.src = "../Images/OjoAbierto.jpeg";
    }
function ocultarContrasena1() 
    {   contrasenaInput1.type = "password";
        imagenOjo1.src = "../Images/OjoCerrado.jpeg";
    }
</script>