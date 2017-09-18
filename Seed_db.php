<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Seed_db
 *
 * @author imartins
 */
class Seed_db {

    public $CI;
    private $nome = "";
    private $idade = "";
    private $senha = "";
    private $email = "";
    private $login = "";
    private $datetime = '';
    
    public $numero_elementos = 10;
    public $idade_minima = 1;
    public $idade_maxima = 30;
    public $valor_minino = 0.01;
    public $valor_maximo = 100.00;
    
    public $datetime_minimo = '-599608800';
    public $datetime_maximo = '5869591199';
    public $cpf_com_pontos = true;
    public $cnpj_com_pontos = true;
    public $tipo_senha = 'md5'; // md5,sha1,alpha, alnum,numeric
    public $numero_caracter_senha = 10;
    public $formato_moeda = array('casa_decimal' => 2, 'separador_dezena' => ',', 'separador_milhar' => '.');
    public $dominios = ['hotmail.com', 'yahoo.com', 'gmail.com', 'globo.com', 'teste.com'];
    public $telefone = "";
    public $cep = "";
    public $mascara_tel = "(##) #####-####";
    
    public $debug = 1;

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->helper('string');
        // $this->setNomes(20);
    }

    public function setConfs($confs) {
        foreach ($confs as $key => $valor) {
            $this->$key = $valor;
        }
    }

    public function seed_($campos, $table = NULL) {
        // remover do commit
        if (!is_null($table)) {
            $this->table = $table;
        }
        $arrays = array();
        //$string_val="";
        $string_val = $string_val_d = array();
        for ($x = 0; $x < $this->numero_elementos; $x++) {
            $fields = array();
            foreach ($campos as $key => $campo) {
                if (strpos($campo, "|")) {
                    $param1 = isset(explode("|", $campo)['1']) ? explode("|", $campo)['1'] : NULL;
                    $campo_mod = explode("|", $campo)['0'];
                    $arrays[$x][$key] = $this->{"get_$campo_mod"}($param1);
                } else {

                    $arrays[$x][$key] = $this->{"get_$campo"}();
                }
                $fields[] = $key;
                $values[] = $arrays[$x][$key];
            }
            $string_sql = "insert into {$this->table}(" . implode(',', $fields) . ") VALUES ";
            $string_val[] = "('" . implode("','", $arrays[$x]) . "')";
            if ($this->debug) {
                $string_val_d[] = "|('" . implode("','", $arrays[$x]) . "')";
                $string_debug = $string_sql . implode(",", $string_val_d) . ";";
            }
        }
        $string_sql .= implode(",", $string_val) . ";";



        if ($this->debug) {
            echo "String de inserção <br>" . str_replace("|", "\n", $string_debug) . '<br>';
            echo "dados inseridos<br><pre>";
            print_r($arrays);
            echo"</pre><br>";
        }
        return true;
    }

    private function get_numeroAleatorio($formato, $mascara = NULL) {
        if (is_null($formato) || !$formato) {
            $formato = 10;
            $this->numero_aleatorio = rand(0, $formato);
        } else if (is_numeric($formato)) {
            $this->numero_aleatorio = rand(0, $formato);
        } else {
            $this->numero_aleatorio = $this->aplicarMascara($formato);
        }

        return $this->numero_aleatorio;
    }

    private function get_textoAleatorio($quant) {
        if (!$quant || $quant == "") {
            $quant = 100;
        }
        $this->texto_aleatorio = random_string('alpha', $quant);
        return $this->texto_aleatorio;
    }

    private function get_cep() {
        $this->cep = $this->aplicarMascara("##.###-###");
        return $this->cep;
    }

    private function get_email() {
        $dominio = $this->dominios;
        $dominio_rand = array_rand($dominio, 1);
        if (empty($this->nome)) {
            $this->get_nome(true);
        }
        $email = $this->removeAcentos($this->nome[0] . "." . $this->nome[1]);
        $this->email = strtolower($email . "@" . $dominio[$dominio_rand]);
        $this->nome = "";
        return $this->email;
    }

    private function get_login() {
        if (empty($this->nome)) {
            $this->get_nome(true);
        }
        $login = $this->removeAcentos($this->nome[0] . "." . $this->nome[1]);
        $this->login = strtolower($login);
        $this->nome = "";
        return $this->login;
    }

    private function get_telefone() {
        $this->telefone = $this->aplicarMascara($this->mascara_tel);
        return $this->telefone;
    }

    private function get_senha() {
        $this->senha = random_string($this->tipo_senha, $this->numero_caracter_senha);

        return $this->senha;
    }

    private function get_cpf() {
        $compontos = $this->cpf_com_pontos;
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - ( $this->mod($d1, 11) );
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - ( $this->mod($d2, 11) );
        if ($d2 >= 10) {
            $d2 = 0;
        }
        $retorno = '';
        if ($compontos == 1) {
            $retorno = '' . $n1 . $n2 . $n3 . "." . $n4 . $n5 . $n6 . "." . $n7 . $n8 . $n9 . "-" . $d1 . $d2;
        } else {
            $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $d1 . $d2;
        }
        return $retorno;
    }

    private function get_cnpj() {
        $compontos = $this->cnpj_com_pontos;
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = 0;
        $n10 = 0;
        $n11 = 0;
        $n12 = 1;
        $d1 = $n12 * 2 + $n11 * 3 + $n10 * 4 + $n9 * 5 + $n8 * 6 + $n7 * 7 + $n6 * 8 + $n5 * 9 + $n4 * 2 + $n3 * 3 + $n2 * 4 + $n1 * 5;
        $d1 = 11 - ( $this->mod($d1, 11) );
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n12 * 3 + $n11 * 4 + $n10 * 5 + $n9 * 6 + $n8 * 7 + $n7 * 8 + $n6 * 9 + $n5 * 2 + $n4 * 3 + $n3 * 4 + $n2 * 5 + $n1 * 6;
        $d2 = 11 - ( $this->mod($d2, 11) );
        if ($d2 >= 10) {
            $d2 = 0;
        }
        $retorno = '';
        if ($compontos == 1) {
            $retorno = '' . $n1 . $n2 . "." . $n3 . $n4 . $n5 . "." . $n6 . $n7 . $n8 . "/" . $n9 . $n10 . $n11 . $n12 . "-" . $d1 . $d2;
        } else {
            $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $n10 . $n11 . $n12 . $d1 . $d2;
        }
        return $retorno;
    }

    private function get_moeda() {
        $num_ref = str_pad('1', $this->formato_moeda['casa_decimal'] + 1, '0', STR_PAD_RIGHT);

        $moeda = rand($this->valor_minino * $num_ref, $this->valor_maximo * $num_ref) / $num_ref;

        return number_format($moeda, $this->formato_moeda['casa_decimal'], $this->formato_moeda['separador_dezena'], $this->formato_moeda['separador_milhar']);
    }

    private function get_idade() {
        $this->idade = rand($this->idade_minima, $this->idade_maxima);

        return $this->idade;
    }

    private function get_year() {
        $this->datetime = rand($this->datetime_minimo, $this->datetime_maximo);
        return date("Y", $this->datetime);
    }

    private function get_datetime() {
        $this->datetime = rand($this->datetime_minimo, $this->datetime_maximo);
        return date("Y-m-d H:i:s", $this->datetime);
    }

    private function get_time() {
        $this->datetime = rand($this->datetime_minimo, $this->datetime_maximo);
        return date("H:i:s", $this->datetime);
    }

    private function get_date() {
        $this->datetime = rand($this->datetime_minimo, $this->datetime_maximo);
        return date("Y-m-d", $this->datetime);
    }

    private function get_nome($return_array = false) {
        $nome = ['Aarão', 'Abdias', 'Abel', 'Abelâmio', 'Abner', 'Abelardo', 'Abílio', 'Abraão', 'Abraim',
            'Abrão', 'Absalão', 'Abssilão', 'Acácio', 'Acilino', 'Acílio', 'Acúrsio',
            'Adail', 'Adalberto', 'Adalsindo', 'Adalsino', 'Adamantino', 'Adamastor', 'Adão',
            'Adauto', 'Adauto', 'Adelindo', 'Adelmiro', 'Adelmo', 'Ademar', 'Ademir', 'Adeodato',
            'Aderico', 'Adério', 'Adérito', 'Adiel', 'Adílio', 'Adner', 'Adolfo', 'Adonai',
            'Adonias', 'Adónias', 'Adonilo', 'Adónis', 'Adorino', 'Adosindo', 'Adriano',
            'Adrião', 'Adriel', 'Adrualdo', 'Adruzilo', 'Afonsino', 'Afonso', 'Afonso',
            'Afrânio', 'Afre', 'Africano', 'Agapito', 'Agenor', 'Agnelo', 'Agostinho', 'Aguinaldo',
            'Aidé', 'Aires', 'Airton', 'Aitor', 'Aladino', 'Alamiro', 'Alan', 'Alano',
            'Alão', 'Alarico', 'Albano', 'Alberico', 'Albertino', 'Alberto', 'Alcibíades', 'Alcides',
            'Alcindo', 'Alcino', 'Aldaír', 'Aldemar', 'Alder', 'Aldo', 'Aldónio', 'Aleixo', 'Aleu',
            'Alex', 'Alexandre', 'Alexandrino', 'Alexandro', 'Aléxio', 'Aléxis', 'Alfeu', 'Alfredo',
            'Alípio', 'Alírio', 'Alítio', 'Alito', 'Alivar', 'Alívio', 'Almerindo', 'Almiro',
            'Almirodo', 'Almurtão', 'Aloís', 'Aloísio', 'Alpoim', 'Altino', 'Alvarim', 'Alvarino',
            'Alvário', 'Álvaro', 'Alvino', 'Amável', 'Ambrósio', 'Américo', 'Amílcar', 'Aminadabe',
            'Amorim', 'Amós', 'Amadeu', 'Amadis', 'Amado', 'Amador', 'Amâncio', 'Amândio',
            'Amarildo', 'Amarílio', 'Amaro', 'Amauri', 'Anacleto', 'Anael', 'Anaim', 'Analide',
            'Anania', 'Ananias', 'Anastácio', 'André', 'Andreias', 'Andreo', 'Andrés', 'Angélico',
            'Ângelo', 'Aníbal', 'Aniceto', 'Anielo', 'Anísio', 'Anolido', 'Anselmo', 'Antão',
            'Antelmo', 'Antenor', 'Antero', 'Antonelo', 'Antonino', 'António', 'Aparício', 'Ápio',
            'Apolinário', 'Apolo', 'Apolónio', 'Aprígio', 'Aquil', 'Aquila', 'Áquila', 'Aquiles',
            'Aquilino', 'Aquino', 'Aquira', 'Aramis', 'Arcádio', 'Arcanjo', 'Arcelino', 'Arcélio',
            'Arcílio', 'Ardingue', 'Argemiro', 'Argentino', 'Ari', 'Ariel', 'Arine', 'Ariosto',
            'Arisberto', 'Aristides', 'Aristóteles', 'Arlindo', 'Armandino', 'Armando', 'Armelim', 'Arménio', 'Armindo',
            'Arnaldo', 'Arnoldo', 'Aron', 'Arquibaldo', 'Arquimedes', 'Arquimínio', 'Arquimino',
            'Arsénio', 'Artur', 'Ary', 'Ascenso', 'Asdrúbal', 'Asélio', 'Áser', 'Assis',
            'Ataíde', 'Atanásio', 'Atão', 'Átila', 'Aubri', 'Augusto', 'Aureliano', 'Aurelino', 'Áureo',
            'Ausendo', 'Austrelino', 'Avelino', 'Aventino', 'Axel', 'Azélio', 'Aziz', 'Azuil',
            'Baqui', 'Barac', 'Barão', 'Bárbaro', 'Barcino', 'Barnabé', 'Bartolomeu',
            'Bartolomeu', 'Basílio', 'Balbino', 'Baldemar', 'Baldomero', 'Balduíno', 'Baltasar', 'Baptista', 'Bassarme',
            'Bastião', 'Batista', 'Bebiano', 'Belarmino', 'Belchior', 'Belisário', 'Belmiro', 'Bendavid', 'Benedito', 'Benevenuto',
            'Benício', 'Benjamim', 'Bento', 'Benvindo', 'Berardo', 'Berilo', 'Bernardim', 'Bernardino',
            'Bernardo', 'Bértil', 'Bertino', 'Berto', 'Bertoldo', 'Bertolino', 'Betino', 'Beto', 'Bianor',
            'Bibiano', 'Boanerges', 'Boaventura', 'Boavida', 'Bonifácio', 'Bóris', 'Brandão', 'Brás',
            'Bráulio', 'Breno', 'Brian', 'Brice', 'Brígido', 'Briolanjo', 'Bruce', 'Bruno', 'Carlo', 'Carlos',
            'Carmério', 'Carmim', 'Carsta', 'Casimiro', 'Cassiano', 'Caetano', 'Caíco', 'Caio', 'Caleb', 'Calisto', 'Calvino',
            'Camilo', 'Cândido', 'Canto', 'Cássio', 'Castelino', 'Castor', 'Catarino', 'Cecílio', 'Cedrico', 'Celestino', 'Celino',
            'Célio', 'Celísio', 'Célsio', 'Celso', 'Celto', 'César', 'Cesário', 'Césaro', 'Charbel', 'Cícero', 'Cid',
            'Cidalino', 'Cildo', 'Cílio', 'Cíntio', 'Cipriano', 'Cireneu', 'Cirilo', 'Ciro', 'Clarindo', 'Claro', 'Claudemiro',
            'Cláudio', 'Clemêncio', 'Clemente', 'Clésio', 'Clídio', 'Clife', 'Clodomiro', 'Clóvis', 'Conrado', 'Constâncio', 'Constantino', 'Consulino',
            'Corsino', 'Cosme', 'Cris', 'Crispim', 'Cristiano', 'Cristofe', 'Cristóforo', 'Cristóvão', 'Cursino',
            'Custó', 'Davi', 'David', 'Davide', 'de Assis', 'Décimo', 'Décio', 'Deivid', 'Dejalme',
            'Délcio', 'Delfim', 'Delfino', 'Délio', 'Delmano', 'Delmar', 'Delmiro', 'Demétrio', 'Dácio',
            'Dagmar', 'Damas', 'Damasceno', 'Damião', 'Daniel', 'Danilo', 'Dante', 'Dárcio', 'Dario',
            'Dário', 'Dener', 'Denil', 'Denis', 'Deodato', 'Deolindo', 'Dércio', 'Deusdedito', 'Dhruva', 'Diamantino',
            'Didaco', 'Diego', 'Dieter', 'Dilan', 'Dilermando', 'Dimas', 'Dinarte', 'Dinis', 'Dino', 'Dioclécio',
            'Diogo', 'Dionísio', 'Diotil', 'Dírio', 'Dirque', 'Divo', 'Djalma', 'Djalme', 'Djalmo', 'Domingos',
            'Domínico', 'Donaldo', 'Donato', 'Donzílio', 'Dóriclo', 'Dorico', 'Dositeu', 'Druso', 'Duarte',
            'Duílio', 'Dulcínio', 'Dúnio', 'Durbalino', 'Durval', 'Durva', 'Edmundo', 'Edmur',
            'Edo', 'Eduardo', 'Eduartino', 'Eduíno', 'Edvaldo', 'Edvino', 'Egas', 'Egídio', 'Egil', 'Eládio',
            'Eleazar', 'Eleutério', 'Elgar', 'Eli', 'Eberardo', 'Eda', 'Eder', 'Edgar', 'Eden', 'Édi',
            'Édipo', 'Edir', 'Edmero', 'Eliab', 'Eliano', 'Elias', 'Eliezer', 'Eliézer', 'Élio', 'Elioenai',
            'Eliseu', 'Elisiário', 'Elísio', 'Elmano', 'Elmar', 'Elmer', 'Elói', 'Elpídio', 'Élsio', 'Élson', 'Élton',
            'Elvino', 'Elzeário', 'Elzo', 'Emanuel', 'Emaús', 'Emídio', 'Emiliano', 'Emílio', 'Emo',
            'Eneias', 'Enes', 'Engrácio', 'Enio', 'Énio', 'Enoque', 'Enrique', 'Enzo', 'Erasmo', 'Ercílio',
            'Eric', 'Erico', 'Érico', 'Erik', 'Erique', 'Ermitério', 'Ernâni', 'Ernesto', 'Esaú', 'Esmeraldo', 'Estanislau',
            'Estefânio', 'Estéfano', 'Estélio', 'Estélio', 'Estevão', 'Estêvão', 'Euclides', 'Eugénio', 'Eulógio', 'Eurico', 'Eusébio',
            'Eustácio', 'Eustáquio', 'Evaldo', 'Evandro', 'Evangelino', 'Evangelista', 'Evaristo', 'Evelácio', 'Evelásio', 'Evélio',
            'Evêncio', 'Everaldo', 'Everardo', 'Expedito', 'Ezequ', 'Fabiano', 'Fabião', 'Fábio', 'Fabrício', 'Falcão', 'Falco',
            'Faustino', 'Fausto', 'Feliciano', 'Felício', 'Felicíssimo', 'Felisberto', 'Felismino', 'Félix', 'Feliz', 'Ferdinando', 'Fernandino',
            'Fernando', 'Fernão', 'Fernão', 'Ferrer', 'Fidélio', 'Filémon', 'Filino', 'Filinto', 'Filipe', 'Filipe',
            'Filipo', 'Filomeno', 'Filoteu', 'Firmino', 'Firmo', 'Flávio', 'Florentino', 'Floriano', 'Florival',
            'Fortunato', 'Fradique', 'Francisco', 'Francisco', 'Francisco', 'Franclim', 'Franco', 'Franklim', 'Franklin', 'Franklino', 'Fred',
            'Frede', 'Frederico', 'Fredo', 'Fulgêncio', 'Fúlvi', 'Gabínio', 'Gabino', 'Gabriel', 'Galiano', 'Galileu',
            'Gamaliel', 'Garcia', 'Garibaldo', 'Gascão', 'Gaspar', 'Gastão', 'Gaudêncio', 'Gávio', 'Gedeão', 'Genésio',
            'Gentil', 'Georgino', 'Geraldo', 'Gerardo', 'Gerberto', 'Germano', 'Gersão', 'Gerson', 'Gervásio', 'Getúlio',
            'Giani', 'Gil', 'Gilberto', 'Gildásio', 'Gildo', 'Gileade', 'Gimeno', 'Ginestal', 'Gino', 'Giovani',
            'Girão', 'Glaúcia', 'Godofredo', 'Goma', 'Gomes', 'Gonçalo', 'Gonzaga', 'Graciano',
            'Graciliano', 'Grácio', 'Gregório', 'Guadalberto', 'Gualdim', 'Gualter', 'Guarani', 'Gueir', 'Gui',
            'Guido', 'Guildo', 'Guilherme', 'Guilhermino', 'Guimar', 'Gumersindo', 'Gumesindo', 'Gusmão', 'Gustavo',
            'Gustavo São Romão', 'Guter', 'Habacuc', 'Habacuque', 'Hamilton', 'Haraldo', 'Haroldo', 'Hazael', 'Héber', 'Heitor',
            'Heldemaro', 'Hélder', 'Heldo', 'Heleno', 'Helier', 'Hélio', 'Heliodoro', 'Hélmut', 'Hélvio',
            'Hemaxi', 'Hemetério', 'Hemitério', 'Henoch', 'Henrique', 'Heraldo', 'Herberto', 'Herculano', 'Herédia', 'Herédio',
            'Heriberto', 'Herlander', 'Hérman', 'Hermano', 'Hermenegildo', 'Hermes', 'Hermínio', 'Hermitério', 'Hernâni', 'Hervê',
            'Higino', 'Hilário', 'Hildeberto', 'Hildebrando', 'Hildegardo', 'Hipólito', 'Hirondino', 'Hólger', 'Homero',
            'Honorato', 'Honório', 'Horácio', 'Huberto', 'Hugo', 'Humbe', 'Iag', 'Iago', 'Ian', 'Ianis',
            'Ibérico', 'Ícaro', 'Idalécio', 'Idálio', 'Idário', 'Idavide', 'Idelso', 'Igor', 'Ildefonso',
            'Ildo', 'Ilídio', 'Inácio', 'Indalécio', 'Indra', 'Indro', 'Infante', 'Ingo', 'Íngue', 'Inocêncio', 'Ioque', 'Irineu', 'Irmino',
            'Isaac', 'Isac', 'Isael', 'Isaí', 'Isaías', 'Isaltino', 'Isandro', 'Isaque', 'Isauro', 'Isidoro',
            'Isidro', 'Isildo', 'Ismael', 'Isolino', 'Israel', 'Ítalo', 'Iúri', 'Ivaldo',
            'Ivan', 'Ivanoel', 'Íven', 'Ivo', 'Izali', 'Jabes', 'Jabim', 'Jacinto', 'Jacó', 'Jacob', 'Jácome', 'Jader', 'Jadir',
            'Jaime', 'Jair', 'Jairo', 'James', 'Jamim', 'Janai', 'Janardo', 'Janique', 'Jansénio', 'Januário', 'Jaque',
            'Jaques', 'Jarbas', 'Jardel', 'Jasão', 'Jasmim', 'Jeremias', 'Jerónimo', 'Jessé', 'Jesualdo', 'Jesus',
            'Jetro', 'Jitendra', 'Joabe', 'João', 'Joaquim', 'Joás', 'Job', 'Jocelino', 'Jociano', 'Joel', 'Jofre',
            'Jonas', 'Jonatã', 'Jónatas', 'Jóni', 'Jordano', 'Jordão', 'Jorge', 'Jório', 'Joscelino',
            'José', 'Josefino', 'Josefo', 'Joselindo', 'Joselino', 'Josias', 'Josselino', 'Josué', 'Jovelino', 'Jovito',
            'Judá', 'Judas', 'Juliano', 'Julião', 'Júlio', 'Júnio', 'Júnior', 'Juno', 'Justiniano', 'Justino',
            'Juvenal', 'Juven', 'Ké', 'Ladislau', 'Lael', 'Laércio', 'Laertes', 'Laudelino', 'Laureano', 'Laurénio',
            'Laurentino', 'Lauriano', 'Laurindo', 'Lauro', 'Lázaro', 'Leal', 'Leandro', 'Leão', 'Léccio', 'Lécio', 'Lemuel', 'Lénio',
            'Leo', 'Leoberto', 'Leonardo', 'Leôncio', 'Leone', 'Leonel', 'Leonício', 'Leónidas', 'Leonídio', 'Leonildo', 'Leopoldo', 'Levi',
            'Liberal', 'Libertário', 'Liberto', 'Lícidas', 'Liciniano', 'Licínio', 'Lício', 'Lídio', 'Lidório', 'Lígio', 'Liliano',
            'Lindorfo', 'Lindoro', 'Lineu', 'Lineu', 'Lino', 'Línton', 'Lisandro', 'Lisuarte', 'Lito', 'Livramento', 'Lopo', 'Loreto',
            'Lorival', 'Lótus', 'Lourenço', 'Lourival', 'Luca', 'Lucas', 'Luciano', 'Lucílio', 'Lucínio', 'Lúcio', 'Ludgero',
            'Ludovico', 'Ludovino', 'Luís', 'Luís Figo', 'Lupicino', 'Lutero', 'Luzio', 'Macário', 'Maciel', 'Madail', 'Madaleno', 'Madate', 'Madjer',
            'Madu', 'Magdo', 'Magno', 'Mago', 'Mair', 'Mamede', 'Manassés', 'Manel', 'Manuel', 'Mapril', 'Mar',
            'Marcelino', 'Marcelo', 'Marcial', 'Marcílio', 'Márcio', 'Marco', 'Marcos', 'Marcus', 'Margarido', 'Mariano',
            'Marílio', 'Marinho', 'Marino', 'Mário', 'Marito', 'Marlon', 'Márlon', 'Marolo', 'Martim', 'Martinho', 'Martiniano',
            'Martino', 'Martins', 'Marto', 'Marvão', 'Márvio', 'Mateus', 'Matias', 'Mátio', 'Matusalém', 'Mauri', 'Maurício',
            'Maurílio', 'Mauro', 'Max', 'Maximiano', 'Maximiliano', 'Maximino', 'Máximo', 'Mel', 'Melchior',
            'Melco', 'Melquisedeque', 'Mélvin', 'Mem', 'Mendo', 'Mesaque', 'Messias', 'Micael', 'Micael de Jesus', 'Miguel', 'Mileu',
            'Milo', 'Milton', 'Mílton', 'Mimon', 'Mimoso', 'Miqueias', 'Mirco', 'Miro', 'Mis', 'Misael', 'Modesto',
            'Moisés', 'Múcio', 'Munir', 'Muril', 'Nabor', 'Nádege', 'Nadir', 'Naod', 'Narsélio', 'Nascimento', 'Nasser',
            'Natã', 'Natalício', 'Natalino', 'Natálio', 'Natanael', 'Nataniel', 'Natão', 'Natércio', 'Nazário', 'Nélio',
            'Nelmo', 'Nelson', 'Nélson', 'Nembrode', 'Nemésio', 'Nemo', 'Nenrode', 'Neóteles', 'Neotero', 'Nereu',
            'Nero', 'Nestor', 'Neutel', 'Nêuton', 'Nicásio', 'Nichal', 'Nicodemos', 'Nicola', 'Nicolau', 'Nídio', 'Niete',
            'Níger', 'Nil', 'Nilo', 'Nilson', 'Nilton', 'Nino', 'Nísio', 'Nivaldo', 'Noah', 'Noame', 'Nobre', 'Noé',
            'Noel', 'Nói', 'Nonato', 'Norberto', 'Norival', 'Normano', 'Nuno', 'Núrio', 'Oceano', 'Octaviano', 'Octávio',
            'Odair', 'Odeberto', 'Ódin', 'Olavo', 'Olegário', 'Olímpio', 'Olindo', 'Olinto', 'Olivar', 'Oliveiros',
            'Olivério', 'Olivier', 'Omar', 'Omer', 'Ondino', 'Onildo', 'Onofre', 'Orandino', 'Orêncio', 'Orestes',
            'Orlandino', 'Orlando', 'Orlindo', 'Orósio', 'Óscar', 'Oseas', 'Oseias', 'Osmano', 'Osmar', 'Osório',
            'Osvaldo', 'Otacílio', 'Otelo', 'Otniel', 'Oto', 'Otoniel', 'Ovídi', 'Pacal', 'Parcidio', 'Parcídio',
            'Páris', 'Pascoal', 'Patrício', 'Paulino', 'Paulo', 'Pedrino', 'Pedro', 'Pelaio', 'Peniel', 'Pepe', 'Pépio',
            'Perfeito', 'Péricles', 'Perpétuo', 'Pérsio', 'Pio', 'Pitágoras', 'Plácido', 'Plínio', 'Policarpo',
            'Pompeu', 'Porfírio', 'Pracídio', 'Prado', 'Priam', 'Prião', 'Primitivo', 'Primo', 'Principiano', 'Priteche', 'Procópio',
            'Próspero', 'Prudê', 'Quaresma', 'Quéli', 'Querubim', 'Quévin', 'Quiliano', 'Quim', 'Quintino', 'Quirilo', 'Quirino', 'Quíri',
            'Râdamas', 'Rafael', 'Rafaelo', 'Ragendra', 'Rai', 'Raimundo', 'Ralfe', 'Ramberto', 'Ramiro', 'Ramna', 'Ramon',
            'Randolfo', 'Rapaz', 'Raúl', 'Ravi', 'Reginaldo', 'Regino', 'Reinaldo', 'Reis', 'Remi', 'Remígio', 'Remízio', 'Renato', 'Reno', 'Requerino', 'Ribca', 'Ricardo', 'Rigoberto', 'Ringo', 'Riu', 'Rivelino', 'Roberto', 'Robim', 'Roboredo', 'Rodolfo', 'Rodrigo', 'Rogélio', 'Rogério', 'Rói', 'Rolando', 'Roli', 'Rolim', 'Romã',
            'Romano', 'Romão', 'Romarico', 'Romarigo', 'Romário', 'Romeu',
            'Rómulo', 'Ronaldo', 'Roque', 'Roriz', 'Rosano', 'Rosário', 'Rosil', 'Rossano', 'Rúben', 'Rubi', 'Rubim', 'Ruby', 'Rudesindo', 'Rúdi', 'Rudolfo', 'Rufino', 'Rui', 'Ruperto', 'Rúpio', 'Rurique', 'Russe', 'Sabino', 'Sacramento', 'Sadi', 'Sadraque',
            'Sadrudine', 'Saladino', 'Salazar', 'Salemo', 'Sales', 'Sáli', 'Salma', 'Salomão', 'Salustiano', 'Salustiniano', 'Salvador', 'Salviano', 'Samaritano', 'Samir', 'Samuel', 'Sancho', 'Sancler', 'Sandrino', 'Sandro', 'Sansão', 'Santana', 'Santelmo',
            'Santiago', 'Sário', 'Sátiro', 'Saúl', 'Saulo', 'Sauro', 'Sávio', 'Sebastião', 'Secundino', 'Segismundo', 'Selésio', 'Seleso',
            'Selmo', 'Sénio', 'Serafim', 'Sereno', 'Sérgio', 'Sertório', 'Sesinando', 'Severiano', 'Severino', 'Severo', 'Siddártha', 'Sidnei', 'Sidónio', 'Sidraque', 'Sifredo', 'Silas', 'Silvano', 'Silvério', 'Silvestre', 'Silviano', 'Sílvio',
            'Simão', 'Simauro', 'Simplício', 'Sindulfo', 'Sinésio', 'Sisenando', 'Sisínio', 'Sisnando', 'Sívio', 'Sixto', 'Sócrates', 'Soeiro', 'Solano', 'Sotero', 'Suraje', 'Susan', 'Taciano', 'Tácio',
            'Tadeu', 'Tálio', 'Tâmiris', 'Tarcísio', 'Tarsício', 'Tasso', 'Tatiano', 'Teliano', 'Telmo', 'Telo', 'Teobaldo', 'Teodemiro', 'Teodomiro', 'Teodoro', 'Teodósio', 'Teófilo', 'Teotónio', 'Tércio', 'Tiago',
            'Tibério', 'Ticiano', 'Tierri', 'Timóteo', 'Tirso', 'Tito', 'Tobias', 'Toledo', 'Tomás', 'Tomé', 'Toni', 'Torcato', 'Torquato', 'Trajano', 'Tristão', 'Tude', 'Túlio', 'Turgo', 'Ubaldo',
            'Udo', 'Ulisses', 'Ulrico', 'Urbano', 'Urbino', 'Urias', 'Uriel', 'Urien', 'Vaíse', 'Valdemar', 'Valdir', 'Valdo', 'Valdomiro', 'Valente', 'Valentim', 'Valentino', 'Valério',
            'Valgi', 'Válter', 'Vando', 'Vânio', 'Varo', 'Vasco', 'Venâncio', 'Venceslau', 'Vêndel', 'Ventura', 'Verdi', 'Vergílio', 'Veridiano', 'Veríssimo',
            'Vero', 'Vérter', 'Vianei', 'Vicêncio', 'Vicente', 'Victor', 'Vidal', 'Vidálio', 'Vidaúl', 'Vilar', 'Vilator', 'Vili', 'Vílmar', 'Vílson', 'Vinício', 'Virgílio', 'Virgínio', 'Virgulino', 'Viriato', 'Vital',
            'Vitaliano', 'Vitálio', 'Vito', 'Vítor', 'Vitorino', 'Vitório', 'Vivaldo', 'Viveque', 'Vladi', 'Wilson', 'William', 'Welton', 'Willy', 'Wallace', 'Wermeson', 'Warley', 'Warle', 'Xavier', 'Xico<',
            'Yuri', 'Yan', 'Yarin', 'Zacarias', 'Zaido', 'Zaido', 'Zaíro', 'Zaqueu', 'Zadeão', 'Zadoque', 'Zafriel', 'Zalman', 'Zarco', 'Zared', 'Zarão', 'Zebadiah', 'Zebilon', 'Zuriel', 'Zózimo', 'Tavernas'];

        $sobrenome = ['Silva', 'Souza', 'Albuquerque', 'Medonça', 'Batista', 'Ferreira', 'Rufino', 'Martins', 'Medeiros', 'Santos', 'Jorge', 'Oliveira', 'Arantes'];


        $nome_rand = array_rand($nome, 1);
        $sn_rand = array_rand($sobrenome, 2);
        $this->nome = array($nome[$nome_rand], $sobrenome[$sn_rand[0]], $sobrenome[$sn_rand[1]]);
        if ($return_array) {
            return $this->nome;
        }
        return implode(" ", $this->nome);
    }

    public function mod($dividendo, $divisor) {
        return round($dividendo - (floor($dividendo / $divisor) * $divisor));
    }

    public function removeAcentos($string) {
        $procurar = array('à', 'ã', 'â', 'é', 'ê', 'í', 'ó', 'ô', 'õ', 'ú', 'ü', 'ç',);
        $substituir = array('a', 'a', 'a', 'e', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'c',);
        $string = strtolower($string);
        $string = str_replace($procurar, $substituir, $string);
        $string = htmlentities($string);
        $string = preg_replace("/&(.)(acute|cedil|circ|ring|tilde|uml);/", "$1", $string);
        //  $string = preg_replace("/([^a-z0-9]+)/", "-", html_entity_decode($string));
        return trim($string, "-");
    }

    private function aplicarMascara($mascara) {
        $retorno = "";
        $this->telefone = "";
        $mascara = str_split($mascara);
        for ($x = 0; $x < count($mascara); $x++) {
            $n = $mascara[$x];
            if ($mascara[$x] == '#') {
                $n = rand(0, 9);
            }
            $retorno .=$n;
        }
        return $retorno;
    }

    public function seed($tables) {
        if (!is_array($tables)) {
            echo "Tables precisa ser um array";
            die();
        }
        $this->createTreeReference($tables);
        //echo "<pre>";print_r($tables);die();

        foreach ($tables as $table => $opcoes) {
            if ($this->CI->db->table_exists($table)) {
                $id_retorno = $this->insertTable($table, $tables);
            } else {
                echo "<br> $table nao existe.<br>";
            }
        }
    }

    private function createTreeReference(&$tables) {
        foreach ($tables as $table => $opcoes) {
            if (isset($opcoes['reference_to']) && count($opcoes['reference_to']) == 1) {
                foreach ($opcoes['reference_to'] as $reference_at => $index) {
                    $tables[$reference_at]['reference_at'][] = $table;
                }
            }
        }
    }

    private function insertTable($table, &$tables, $id_retorno = NULL) {
        if (!isset($tables[$table])) {
            return NULL;
        }

        $campos = $this->CI->db->field_data($table);
        $opcoes = $tables[$table];
        $numero_elemento = (isset($opcoes['numero_elementos']) && $opcoes['numero_elementos']>0)?$opcoes['numero_elementos']:$this->numero_elementos;
        for ($x = 0; $x < $numero_elemento; $x++) {
            if($this->debug){
                echo "inserindo na tabela $table<br>";
            }
            if ($opcoes == "*") {
                $opcoes = array('exclude_fields' => array(), 'fields' => array());
            } else {
                if (!isset($opcoes['exclude_fields'])) {
                    $opcoes['exclude_fields'] = array();
                }
                if (!isset($opcoes['fields']) || count($opcoes['fields']) == 0 || ($opcoes['fields']) == "") {
                    $opcoes['fields'] = array();
                }
            }
            $dados = array();
            foreach ($campos as $key => $campo) {

                if ($campo->primary_key || in_array($campo->name, $opcoes['exclude_fields'])) {
                    continue;
                }
                if (in_array($campo->name, array_flip($opcoes['fields']))) {
                    //  echo "{$campo->name} esta<br>";
                    $dados[$campo->name] = $this->getValueField($opcoes['fields'][$campo->name]);
                } else {
                    $setfield = $this->setField($campo);
                    $dados[$campo->name] = $setfield; //"ainda vou ver";
                }
                if (isset($opcoes['reference_to']) && count($opcoes['reference_to']) == 1 && !is_null($id_retorno)) {
                    $campo_db = explode('->', current($opcoes['reference_to']));
                    $dados[$campo_db[0]] = $id_retorno;
                }
            }
            $this->CI->db->insert("$table", $dados);
            $id_retorno_table = $this->CI->db->insert_id();
            if (isset($opcoes['reference_at'])) {
                foreach ($opcoes['reference_at'] as $table_ref) {
                    $this->insertTable($table_ref, $tables, $id_retorno_table);
                }
            }
        }
        if (isset($opcoes['reference_at'])) {
            foreach ($opcoes['reference_at'] as $table_ref) {
                unset($tables[$table_ref]);
            }
        }
        return $id_retorno;
    }

    private function setField($campo) {
        if (method_exists($this, "get_{$campo->name}")) {
            return $this->{"get_$campo->name"}();
        }
        switch ($campo->type) {
            case 'integer': case 'int': case 'smallint': case 'tinyint': case 'mediumint': case 'bigint':
            case 'decimal': case 'numeric': case 'float': case 'double': case 'bit':
                return $this->get_numeroAleatorio($campo->max_length);
                break;
            case 'char': case 'varchar': case 'text': case 'longtext':case 'mediumtext': case 'tinytext': case 'blob': case 'longblob': case 'mediumblob': case 'tinyblob':
            case 'binary': case 'varbinary': case 'enum': case 'set':
                return $this->get_textoAleatorio($campo->max_length);

            case 'year':
                return $this->get_year();

            case 'datetime': case 'timestamp':
                return $this->get_datetime();
            case 'time':
                return $this->get_time();
            case 'date':
                return $this->get_date();
            default:
                return $this->get_textoAleatorio(10);
                break;
        }
    }

    private function getValueField($campo) {
        if (strpos($campo, "|")) {
            $param1 = isset(explode("|", $campo)['1']) ? explode("|", $campo)['1'] : NULL;
            $campo_mod = explode("|", $campo)['0'];
            return $this->{"get_$campo_mod"}($param1);
        } else {
            return $this->{"get_$campo"}();
        }
    }

}
