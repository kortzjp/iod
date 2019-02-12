<?php

require_once './core/Template.php';

class AsistenciasView {

    public function home() {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_home.html");
        print $header;
        print $contenido;
        print $footer;
    }

    public function mostrar_lista_asistencias($idCurso, $clases, $alumnos, $asistencias_datos, $existenDatos) {
        $mes = new DateTime($clases[0]);
        $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO",
            "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        $m = date("n", strtotime($mes->format('Y-m-d')));

        $datos = array('curso' => $idCurso, 'mes' => $meses[$m - 1], 'asignatura' => $_POST['asignatura']);
        $obj = (object) $datos;

        $dias = array();
        $asistencias = array();

        $totalDias = count($clases);

        for ($d = 0; $d < $totalDias; $d++) {

            $asis = array('dia' => $clases[$d]);
            $fech = (object) $asis;
            $asistencias[] = $fech;

            $uno = new DateTime($clases[$d]);
            $dia = array('dia' => $uno->format('d'));
            $fecha = (object) $dia;
            $dias[] = $fecha;
        }

        // obtener la lista de alumnos
        $auxAlumno = array();
        $n = 0;

        foreach ($alumnos as $row => $alumno) {

            $auxAlumno[$n]['n'] = ($n + 1);
            $auxAlumno[$n]['id'] = $alumno['id'];
            $auxAlumno[$n]['matricula'] = $alumno['matricula'];
            $auxAlumno[$n]['nombre'] = $alumno['nombre'];
            $auxAlumno[$n]['estado'] = $alumno['estado'];

            $suma = 0;
            $d = 0;

            foreach ($asistencias_datos as $key => $unAlumno) {
                foreach ($clases as $key => $unDia) {
                    if ($unAlumno['id'] == $alumno['id'] && $unAlumno['dia'] == $unDia) {
                        $auxAlumno[$n][$unDia] = $unAlumno['asistencia'];
                        $d++;
                        $suma += $unAlumno['asistencia'];
                    }
                }
            }

            while ($d < $totalDias) {
                $auxAlumno[$n][$clases[$d]] = '<label> 0 <input type="radio" name="alumno[' . $auxAlumno[$n]['id'] . '_' . $clases[$d] . ']" value="0" ></label><br>'
                        . '<label> 1 <input type="radio" name="alumno[' . $auxAlumno[$n]['id'] . '_' . $clases[$d] . ']" value="1" checked ></label>';
                ;
                
                $d++;
            }

            $auxAlumno[$n]['porcentaje'] = number_format($suma * 100 / $totalDias, 0, '.', '');
            $n++;
        }


        $lista = array();

        for ($a = 0; $a < count($auxAlumno); $a++) {
            //$aa = array( 'dia' => $clases[$d] );
            $alum = (object) $auxAlumno[$a];
            $lista[] = $alum;
        }

        $str = file_get_contents('./public/html/asistencias/asistencias_lista.html');

        $tmpl = new Template($str);
        $str = $tmpl->render($obj); // titulos de la página

        $tmpl = new Template($str);
        $str = $tmpl->render_regex($dias, 'DIA');   // celdas de encabezado DIA

        $tmpl = new Template($str);
        $str = $tmpl->render_regex($asistencias, 'DIAS');   // celdas de fecha de asistencia DIAS

        $tmpl = new Template($str);
        $this->asistencias($tmpl->render_regex($lista, 'ALUMNOS'));   // celdas de alumnos ALUMNOS
    }

    public function mostrar_lista($idCurso, $clases, $asistencias_datos, $existenDatos) {
        $mes = new DateTime($clases[0]);
        $meses = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO",
            "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
        $m = date("n", strtotime($mes->format('Y-m-d')));

        $datos = array('curso' => $idCurso, 'mes' => $meses[$m - 1], 'asignatura' => $_POST['asignatura']);
        $obj = (object) $datos;

        $dias = array();
        $asistencias = array();

        $totalDias = count($clases);

        for ($d = 0; $d < $totalDias; $d++) {

            $asis = array('dia' => $clases[$d]);
            $fech = (object) $asis;
            $asistencias[] = $fech;

            $uno = new DateTime($clases[$d]);
            $dia = array('dia' => $uno->format('d'));
            $fecha = (object) $dia;
            $dias[] = $fecha;
        }

        // obtener la lista de alumnos
        $matricula = '';
        $auxAlumno = array();
        $n = -1;
        $cambio = true;
        $suma = 0;
        $totalDiasRegistrados = 0;

        foreach ($asistencias_datos as $row => $alumno) {
            // para cambiar de nombre en la lista
            if (strcmp($alumno['matricula'], $matricula) !== 0) {
                $matricula = $alumno['matricula'];
                $n++;
                if ($n >= 1) {
                    if ($totalDiasRegistrados < $totalDias) {
                        for ($d = $totalDiasRegistrados; $d < $totalDias; $d++) {
                            $auxAlumno[$n - 1][$clases[$d]] = '<label> 0 <input type="radio" name="alumno[' . $auxAlumno[$n - 1]['id'] . '_' . $clases[$d] . ']" value="0" ></label><br>'
                                  . '<label> 1 <input type="radio" name="alumno[' . $auxAlumno[$n - 1]['id'] . '_' . $clases[$d] . ']" value="1" checked ></label>';
                            
                        }
                    }
                    $auxAlumno[$n - 1]['porcentaje'] = number_format($suma * 100 / $totalDias, 0, '.', '');
                    $suma = 0;
                }

                $auxAlumno[$n]['n'] = ($n + 1);
                $auxAlumno[$n]['id'] = $alumno['id'];
                $auxAlumno[$n]['matricula'] = $matricula;
                $auxAlumno[$n]['nombre'] = $alumno['nombre'];
                $auxAlumno[$n]['estado'] = $alumno['estado'];
                if ($existenDatos) {
                    $auxAlumno[$n][$alumno['dia']] = $alumno['asistencia'];
                    $suma += $alumno['asistencia'];
                } else {
                    $auxAlumno[$n][$clases[0]] = '<label>0<input type="radio" name="alumno[' . $auxAlumno[$n]['id'] . '_' . $clases[0] . ']" value="0" ></label><br>'
                            . '<label>1<input type="radio" name="alumno[' . $auxAlumno[$n]['id'] . '_' . $clases[0] . ']" value="1" checked ></label>';
                }
                $totalDiasRegistrados = 1;
            } else {
                $auxAlumno[$n][$alumno['dia']] = $alumno['asistencia'];
                $suma += $alumno['asistencia'];
                $totalDiasRegistrados++;
            }
        }

        if ($totalDiasRegistrados < $totalDias) {
            for ($d = $totalDiasRegistrados; $d < $totalDias; $d++) {
                $auxAlumno[$n][$clases[$d]] = '<label>0<input type="radio" name="alumno[' . $auxAlumno[$n]['id'] . '_' . $clases[$d] . ']" value="0" ></label><br>'
                        . '<label>1<input type="radio" name="alumno[' . $auxAlumno[$n]['id'] . '_' . $clases[$d] . ']" value="1" checked ></label>';
            }
        }

        $auxAlumno[$n]['porcentaje'] = number_format($suma * 100 / $totalDias, 0, '.', '');

        $lista = array();

        for ($a = 0; $a < count($auxAlumno); $a++) {
            //$aa = array( 'dia' => $clases[$d] );
            $alum = (object) $auxAlumno[$a];
            $lista[] = $alum;
        }

        $str = file_get_contents('./public/html/asistencias/asistencias_lista.html');

        $tmpl = new Template($str);
        $str = $tmpl->render($obj); // titulos de la página

        $tmpl = new Template($str);
        $str = $tmpl->render_regex($dias, 'DIA');   // celdas de encabezado DIA

        $tmpl = new Template($str);
        $str = $tmpl->render_regex($asistencias, 'DIAS');   // celdas de fecha de asistencia DIAS

        $tmpl = new Template($str);
        $this->asistencias($tmpl->render_regex($lista, 'ALUMNOS'));   // celdas de alumnos ALUMNOS
    }

    public function asistencias($contenido) {
        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");

        print $header;
        print $contenido;
        print $footer;
    }

    public function mensaje($tipo, $titulo, $mensaje) {

        $usuario = $_SESSION['usuario'];
        $nombre = $_SESSION['nombre'];

        $header = file_get_contents("./public/html/docente/docente_header.html");
        $datosHeader = array('usuario' => $usuario, 'nombre' => $nombre);

        $tmpl = new Template($header);
        $header = $tmpl->render($datosHeader);

        $footer = file_get_contents("./public/html/docente/docente_footer.html");
        $contenido = file_get_contents("./public/html/docente/docente_mensaje.html");

        $datos = array('tipo' => $tipo, 'titulo' => $titulo, 'mensaje' => $mensaje);

        $tmpl = new Template($contenido);
        $contenido = $tmpl->render($datos);

        print $header;
        print $contenido;
        print $footer;
    }

}
