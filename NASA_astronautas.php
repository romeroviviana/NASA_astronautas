<?php
/**
* Plugin Name: NASA Astronautas
* Author: Vivina Romero
* Description: Plugin para registrar datos de un formulario
*/
register_activation_hook(__FILE__,'nasa_aspirante_init');

function nasa_aspirante_init(){
	global $wpdb; 
    $tabla_aspirantes = $wpdb->prefix . 'nasa_form';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $query = "CREATE TABLE IF NOT EXISTS $tabla_aspirantes (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(40) NOT NULL,
        edad smallint(2) NOT NULL,
        sexo varchar(40) NOT NULL,
        correo varchar(100) NOT NULL,
        motivo varchar(400) NOT NULL,
        extraterrestre varchar(400) NOT NULL,
        created_at datetime NOT NULL,
        UNIQUE (id)
        ) $charset_collate;";
    
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query); 

}

add_shortcode('nasa_plugin_form','NASA_Plugin_form');

function NASA_Plugin_form(){
	global $wpdb; 
    if (!empty($_POST) AND $_POST['nombre'] != ''
    	AND wp_verify_nonce($_POST['aspirante_nonce'], 'graba_aspirante')
		) {
        $tabla_aspirantes = $wpdb->prefix . 'nasa_form'; 
        $nombre = $_POST['nombre'];
        $edad = $_POST['edad'];
        $sexo = $_POST['sexo'];
        $correo = $_POST['correo'];
        $motivo = $_POST['motivo'];
        $extraterrestre = $_POST['extraterrestre'];
        $created_at = date('Y-m-d H:i:s');

        $wpdb->insert(
            $tabla_aspirantes,
            array(
                'nombre' => $nombre,
                'edad' => $edad,
                'sexo' => $sexo,
                'correo' => $correo,
                'motivo' => $motivo,
                'extraterrestre' => $extraterrestre,
                'created_at' => $created_at,
            )
        );
        echo "<p class='exito'>". get_option( 'nasa_agradecimiento' )."<p>";
        
    }


	ob_start();
	?>
    <h2><?php echo get_option( 'nasa_introduccion' ); ?></h2>
    // <img src="<?php echo get_option( 'nasa_logo' ); ?>" >
	<form action="<?php get_the_permalink(); ?>" method="post" id="form_aspirante"
      class="cuestionario">
      <?php wp_nonce_field('graba_aspirante', 'aspirante_nonce'); ?>
		<input type="text" name="nombre" id="nombre" placeholder="Nombre Completo" required>

		<input type="text" name="edad" id="edad" placeholder="Edad" required>

		<select name="sexo" id="sexo" required>
			<option value="" disabled selected>Sexo</option>
			<option value="Femenino">Femenino</option>
			<option value="Masculino">Masculino</option>
		</select>

		<input type="email" name="correo" id="correo" placeholder="Correo Electrónico." required>

		<label>Motivos para ir a la luna</label>
		<textarea name="motivo" id="motivo" cols="30" rows="10" required></textarea>

		<label>Última vez que tuvo contacto con extraterrestres.</label>
		<textarea name="extraterrestre" id="extraterrestre" cols="30" rows="10" required></textarea>

		<input type="submit" value="Enviar">
	</form>


	<?php
	return ob_get_clean();
}


add_action("admin_menu", "nasa_form_menu");

function nasa_form_menu(){
	add_menu_page('NASA clientes ', 'NASA clientes ', 'manage_options', 
        'nasa_menu', 'nasa_form_admin', 'dashicons-feedback', 75);
	add_submenu_page('nasa_menu','Configuración', 'Configuración','manage_options','nasa_menu_configuracion','nasa_form_configuracion');

	add_submenu_page('nasa_menu','Reporte', 'Reporte','manage_options','nasa_menu_reporte','nasa_form_reporte');
}

function nasa_form_admin(){
	

}
function nasa_form_configuracion(){
     echo("<div class='wrap'><h2>Configuración de formulario</h2></div>");

    if(isset($_POST['action']) && $_POST['action'] == "nasaopciones"){
        update_option('nasa_agradecimiento',$_POST['agradecimiento']);
        update_option('nasa_logo',$_POST['logo']);
        update_option('nasa_introduccion',$_POST['introduccion']);
        echo("<div class='updated message' style='padding: 10px'>Opciones guardadas.</div>");
    }

    ?>

    <form method='post'>
        <input type='hidden' name='action' value='nasaopciones'>
        <table>
            <tr>
                <td>
                    <label for='agradecimiento'>Mensaje de agradecimiento</label>
                </td>
                <td>
                    <input type='text' name='agradecimiento' id='agradecimiento' value='<?=get_option('nasa_agradecimiento')?>'>
                </td>
            </tr>
            <tr>
                <td>
                    <label for='logo'>Logo del encabezado</label>
                </td>
                <td>
                    <input type='file' name='logo' id='logo' value='<?=get_option('nasa_logo')?>'>
                </td>
            </tr>
            <tr>
                <td>
                    <?php if(get_option('nasa_logo')){ ?>
                       <img src="<?php get_the_permalink(); ?><?=get_option('nasa_logo')?>">
                    <?php } ?>
                    
                </td>
            </tr>
            <tr>
                <td>
                    <label for='introduccion'>Mensaje de introducción</label>
                </td>
                <td>
                    <input type='text' name='introduccion' id='introduccion' value='<?=get_option('nasa_introduccion')?>'>
                </td>
            </tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' value='Enviar'>
                </td>
            </tr>
        </table>
    </form>

    <?php
}
function nasa_form_reporte(){
	global $wpdb;
	$tabla_aspirantes = $wpdb->prefix . 'nasa_form'; 

	echo '<div class="wrap"><h1>Lista de datos del formulario</h1>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th width="30%">Nombre</th><th width="20%">Edad</th>
        <th>Sexo</th><th>Correo Electrónico</th><th>Motivos para ir a la luna</th>
        <th>Ultimo contacto con extraterrestre</th></tr></thead>';
    echo '<tbody id="the-list">';
    $aspirantes = $wpdb->get_results("SELECT * FROM $tabla_aspirantes");
    foreach ( $aspirantes as $aspirante ) {
        $nombre = esc_textarea($aspirante->nombre);
        $edad = esc_textarea($aspirante->edad);
        $sexo = esc_textarea($aspirante->sexo);
        $correo = esc_textarea($aspirante->correo);
        $motivo = esc_textarea($aspirante->motivo);
        $extraterrestre = esc_textarea($aspirante->extraterrestre);
        echo "<tr><td><a href='#' title='$nombre'>$nombre</a></td>
            <td>$edad</td><td>$sexo</td><td>$correo</td>
            <td>$motivo</td><td>$extraterrestre</td>
            </tr>";
    }
    echo '</tbody></table></div>';
}