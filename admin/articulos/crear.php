<?php 
    //incluyendo la base de datos
    require '../../includes/config/database.php';


    require '../../includes/funciones.php';

    //accediendo a la variable de sesión del arreglo session
    $auth = estaAutenticado();

    //limitar el acceso a ciertas páginas si no está autenticado
    if(!$auth) {
        header('Location: /');
    }




    $db = conectarDB();

    // Consultar para obtener los vendedores
    $consulta = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $consulta);

    // Arreglo con mensajes de errores
    $errores = [];

    $titulo = '';
    $precio = '';
    $descripcion = '';
    $tipo = '';
    $vendedorId = '';


   // Ejecutar el código después de que el usuario envia el formulario
   if($_SERVER['REQUEST_METHOD'] === 'POST') {

        // echo "<pre>";
        // var_dump($_POST);
        // echo "</pre>";


        echo "<pre>";
        var_dump($_FILES);
        echo "</pre>";

        //exit; //esto es para que no se ejecute el código de abajo

        //usando mysqli_real_escape_string para evitar inyecciones sql "sanitizar"
        $titulo = mysqli_real_escape_string( $db,  $_POST['titulo'] );
        $precio = mysqli_real_escape_string( $db,  $_POST['precio'] );
        $descripcion = mysqli_real_escape_string( $db,  $_POST['descripcion'] );
        $tipo = mysqli_real_escape_string( $db,  $_POST['tipo'] );
        $vendedorId = mysqli_real_escape_string( $db,  $_POST['vendedor'] );
        $creado = date('Y/m/d');

        // Asignar files hacia una variable
        $imagen = $_FILES['imagen'];


        //validando los datos
        if(!$titulo) {
            $errores[] = "Debes añadir un titulo";
        }

        if(!$precio) {
            $errores[] = 'El Precio es Obligatorio';
        }

        if( strlen( $descripcion ) < 50 ) {
            $errores[] = 'La descripción es obligatoria y debe tener al menos 50 caracteres';
        }

        if(!$vendedorId) {
            $errores[] = 'Elige un vendedor';
        }

        if(!$imagen['name'] || $imagen['error'] ) {
            $errores[] = 'La Imagen es Obligatoria';
        }

        // Validar por tamaño (1mb máximo)
        $medida = 1000 * 1000;


        if($imagen['size'] > $medida ) {
            $errores[] = 'La Imagen es muy pesada';
        }

        if(!$tipo) {
            $errores[] = "Añade a qué tipo de artículo pertenece";
        }



        // Revisar que el arreglo de errores esté vacío
        if(empty($errores)) {

            /** SUBIDA DE ARCHIVOS */

            // Crear carpeta
            $carpetaImagenes = '../../imagenes/';

            if(!is_dir($carpetaImagenes)) {
                mkdir($carpetaImagenes);
            }

            // Generar un nombre único
            $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";


            // Subir la imagen
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );
 

            $query = "INSERT INTO articulos (titulo, precio, imagen, descripcion, tipo, creado, vendedor_id) VALUES ('$titulo', '$precio', '$nombreImagen','$descripcion', '$tipo', '$creado', '$vendedorId')";

            //echo $query;

            $resultado = mysqli_query($db, $query); //con esto la consulta se ejecuta en la base de datos

            if($resultado) {
                // Redireccionar al usuario.
                header('Location: /admin?resultado=1');
            }
        }

    }
    
    incluirTemplate('header');
?>

    <main class="contenedor seccion">
        <h1>Crear</h1>

        <a href="/admin" class="boton boton-verde">Regresar</a>

        <!-- //si hay 7 mensajes de error, se mostrarán -->
        <?php foreach($errores as $error): ?>
        <div class="alerta error">
            <?php echo $error; ?>
        </div>
        <?php endforeach; ?>


        <!--enctype="multipart/form-data" es para que el formulario pueda subir archivos-->
        <form class="formulario" method="POST" action="/admin/articulos/crear.php" enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Título:</label> 
                <input type="text" id="titulo" name="titulo" placeholder="Titulo del Artículo" value="<?php echo $titulo; ?>"> <!-- el value sirve para que no se borren los datos al haber un error -->

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio del Articulo" value="<?php echo $precio; ?>">

                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>

                <label for="tipo">Tipo:</label>
                <input type="text" id="tipo" name="tipo" placeholder="Tipo de Articulo" value="<?php echo $tipo; ?>">

            </fieldset>
            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="">-- Seleccione --</option>
                    <?php while($vendedor =  mysqli_fetch_assoc($resultado) ) : ?>
                        <option  <?php echo $vendedorId === $vendedor['id'] ? 'selected' : ''; ?>   value="<?php echo $vendedor['id']; ?>"> <?php echo $vendedor['nombre'] . " " . $vendedor['apellido']; ?> </option>
                    <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Crear Articulo" class="boton boton-verde">

           
                        
        </form>
        
    </main>

<?php 
    incluirTemplate('footer');
?>