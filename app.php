<?php

//classe dashboard
class Dashboard {
    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $total_vendas;
    public $despesas;
    public $clientesAtivos;
    public $clientesInativos;
    public $reclamacoes;
    public $elogios;
    public $sugestoes;

    public function __get($attr) {
        return $this->$attr;
    }

    public function __set($attr, $value){
        $this->$attr = $value;
        return $this;
    }
}

// Conexão
class Conexao {
    private $host = 'localhost';
    private $dbname = 'dashboard';
    private $user = 'root';
    private $pass = '';

    public function conectar() {
        try {
            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                $this->user,
                $this->pass
            );
            $conexao->exec('set charset utf8');
            return $conexao;
        } catch (PDOException $e) {
            echo '<p>' . $e->getMessage() . '</p>';
        }
    }
}

// Model
class Bd {
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard) {
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

    public function getDespesas() {
        $query = '
            select 
                SUM(total) as despesas 
            from 
                tb_despesas 
            where 
                data_despesa between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->despesas;
    }

    public function getClientesAtivos() {
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

    public function getClientesInativos() {
        $query = '
            select
                count(*) as clientesInativos
            from
                tb_clientes
            where
                cliente_ativo = 0';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->clientesInativos;
    }

    public function getReclamacoes(){
        $query = '
            select
                count(*) as reclamacoes
            from
                tb_contatos
            where
                tipo_contato = 2;
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->reclamacoes;
    }

    public function getElogios(){
        $query = '
            select
                count(*) as elogios
            from
                tb_contatos
            where
                tipo_contato = 1;
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->elogios;
    }

    public function getSugestoes(){
        $query = '
            select
                count(*) as sugestoes
            from
                tb_contatos
            where
                tipo_contato = 3;
        ';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->sugestoes;
    }
}

$conexao = new Conexao();

// === Apenas clientes ativos ===
if (isset($_GET['dadosFixos']) && $_GET['dadosFixos'] == 'true') {
    $dashboardClientes = new Dashboard();
    $bdClientes = new Bd($conexao, $dashboardClientes);
    
    $dashboardClientes->__set('clientesAtivos', $bdClientes->getClientesAtivos());

    $dashboardClientes->__set('clientesInativos', $bdClientes->getClientesInativos());

    $dashboardClientes->__set('reclamacoes', $bdClientes->getReclamacoes());

    $dashboardClientes->__set('elogios', $bdClientes->getElogios());

    $dashboardClientes->__set('sugestoes', $bdClientes->getSugestoes());

    echo json_encode([
        'clientesAtivos' => $dashboardClientes->__get('clientesAtivos'),
        'clientesInativos' => $dashboardClientes->__get('clientesInativos'),
        'reclamacoes' => $dashboardClientes->__get('reclamacoes'),
        'elogios' => $dashboardClientes->__get('elogios'),
        'sugestoes' => $dashboardClientes->__get('sugestoes')
    ]);
    exit;
}

// === Dashboard por competência ===
$dashboard = new Dashboard();

if (isset($_GET['competencia'])) {
    $competencia = explode('-', $_GET['competencia']);
    $ano = $competencia[0];
    $mes = $competencia[1];
    $diasDoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    $dashboard->__set('data_inicio', "$ano-$mes-01");
    $dashboard->__set('data_fim', "$ano-$mes-$diasDoMes");

    $bd = new Bd($conexao, $dashboard);

    $dashboard->__set('numeroVendas', $bd->getNumeroVendas());
    $dashboard->__set('total_vendas', $bd->getTotalVendas());
    $dashboard->__set('despesas', $bd->getDespesas());

    echo json_encode([
        'dashboard' => $dashboard
    ]);
    exit;
}

// Se nenhuma condição for satisfeita
echo json_encode(['erro' => 'Requisição inválida.']);
