<?php
//clase dashboard
class dashboard{
    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $total_vendas;
    public $clentesAtivos;

    public function __get($atrr) {
        return $this->$atrr;
    }

    public function __set($atrr, $value){
        $this->$atrr = $value;
        return $this;
    }
}

//Conexão
class Conexao{
    private $host = 'localhost';
    private $dbname = 'dashboard';
    private $user = 'root';
    private $pass = '';

    public function conectar() {
        try{

            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname","$this->user",
                "$this->pass"
            );
            
            //utf8
            $conexao->exec('set charset utf8');

            return $conexao;

        } catch (PDOException $e){
            echo '<p>'.$e->getMessage().'</p>';
        }
    }
}

//classe (model)
class Bd {
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard){
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }

    public function getNumeroVendas() {
        $query = '
            select 
                count(*) as numero_vendas 
            from 
                tb_vendas 
            where 
                data_venda between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }

    public function getTotalVendas() {
        $query = '
            select 
                SUM(total) as total_vendas 
            from 
                tb_vendas 
            where 
                data_venda between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }

    public function getClientesAtivos(){
        $query = '
            select
                count(*) as clientesAtivos
            from
                tb_clientes
            where
                cliente_ativo = 1';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientesAtivos;
    }
}

//Lógica Script
$dashboard = new Dashboard();
$dashboardClientes = new Dashboard();

$conexao = new Conexao();

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];

$diasDoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano. '-' . $mes. '-' . '01');
$dashboard->__set('data_fim', $ano. '-' . $mes. '-' . $diasDoMes);

$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numeroVendas', $bd->getNumeroVendas());

$dashboard->__set('total_vendas', $bd->getTotalVendas());

$dashboard->__set('clientesAtivos', $bd->getClientesAtivos());


echo json_encode($dashboard);

?>