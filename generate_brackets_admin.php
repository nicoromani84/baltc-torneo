<?php
/**
 * Script administrativo para generar cuadros de semifinales y finales
 * 
 * Uso: Ejecutar desde Admin o CLI para generar:
 * - 1ra Caballeros: Semifinales cruzadas (1ro A vs 2do B, 1ro B vs 2do A)
 * - 1ra Damas: Semifinales cruzadas
 * - 4ta Damas: Final (1ro A vs 1ro B)
 */

// Configuración
define('BASEPATH', __DIR__);
define('ENVIRONMENT', 'production');

// Cargar CI
require_once 'index.php';

// No ejecutar directamente en web
if(php_sapi_name() !== 'cli' && !isset($_GET['admin_key'])) {
    die('Access denied');
}

// Conectar a BD
$CI = & get_instance();
$CI->load->model('Partido_model');

// Función para calcular standings de grupo
function getGroupStandings($db, $category, $gender, $grupo) {
    $sql = "
        SELECT 
            m.jugador1_id,
            m.jugador2_id,
            m.ganador_id,
            m.score
        FROM matches m
        WHERE m.category = $category
        AND m.gender = '$gender'
        AND m.ronda = '$grupo'
        AND m.ganador_id IS NOT NULL
        ORDER BY m.timestamp DESC
    ";
    
    $res = $db->query($sql);
    $standings = [];
    
    while($row = $res->fetch_assoc()) {
        $j1 = $row['jugador1_id'];
        $j2 = $row['jugador2_id'];
        $ganador = $row['ganador_id'];
        
        if(!isset($standings[$j1])) {
            $standings[$j1] = ['pts' => 0, 'pg' => 0, 'pp' => 0];
        }
        if(!isset($standings[$j2])) {
            $standings[$j2] = ['pts' => 0, 'pg' => 0, 'pp' => 0];
        }
        
        // Score parsing (W.O., sets, etc.)
        $score = $row['score'];
        if(strpos(strtoupper($score), 'W.O.') !== false) {
            if($ganador == $j1) {
                $standings[$j1]['pts'] += 1;
                $standings[$j1]['pg'] += 2;
                $standings[$j2]['pp'] += 2;
            } else {
                $standings[$j2]['pts'] += 1;
                $standings[$j2]['pg'] += 2;
                $standings[$j1]['pp'] += 2;
            }
        } else {
            $sets = explode(',', $score);
            $j1_sets = 0;
            $j2_sets = 0;
            
            foreach($sets as $set) {
                $games = explode('-', trim($set));
                if(count($games) == 2) {
                    $g1 = (int)$games[0];
                    $g2 = (int)$games[1];
                    if($g1 > $g2) $j1_sets++;
                    else $j2_sets++;
                }
            }
            
            if($ganador == $j1) {
                $standings[$j1]['pts'] += 1;
                $standings[$j1]['pg'] += $j1_sets;
                $standings[$j2]['pp'] += $j2_sets;
            } else {
                $standings[$j2]['pts'] += 1;
                $standings[$j2]['pg'] += $j2_sets;
                $standings[$j1]['pp'] += $j1_sets;
            }
        }
    }
    
    uasort($standings, function($a, $b) {
        return $b['pts'] <=> $a['pts'];
    });
    
    return array_keys(array_slice($standings, 0, 3, true));
}

echo "✓ Script de generación de brackets cargado\n";
?>
