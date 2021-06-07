<?php
namespace App\BusinessLayer\Features\Livros;

use Illuminate\Support\Arr;
use App\BusinessLayer\ResponseHttpCode;
use App\Exceptions\CamposObrigatoriosInvalidosException;

// Importo DTOs
use App\DataLayer\Dtos\PesquisarLivrosDto;

// Importo validators
use App\BusinessLayer\Validators\Livros\PesquisarLivrosValidator;

// Importo actions
use App\BusinessLayer\Actions\Livros\PesquisarLivrosAction;

// Importo resources
use App\Http\Resources\LivroCollection;

class PesquisarLivrosFeature {

    // Defino variaveis
    private $definition = 'Responsável por agrupar e executar uma ou mais ações que serão utilizadas num determinado propósito';
    private $pesquisarLivrosAction;

    /**
     * 
     * Método construtor
     *
     * @access public
     * @param PesquisarLivrosAction $pesquisarLivrosAction
     * @return void
     * 
     */
    public function __construct(
        PesquisarLivrosAction $pesquisarLivrosAction
    ) {

        // Instancio actions
        $this->PesquisarLivrosAction = $pesquisarLivrosAction;

    }



    /**
     * 
     * Executa tarefa única da classe
     *
     * @access public
     * @param PesquisarLivrosDto $pesquisarLivrosDto
     * @return array
     * 
     */
    public function execute(PesquisarLivrosDto $pesquisarLivrosDto) : array {

        // Converto objeto para array
        $dados = $pesquisarLivrosDto->toArray();

        /*// Validação de dados obrigatórios
        $validador = new PesquisarLivrosValidator;
        $validador->execute($dados);

        // Caso os dados NÃO SEJAM válidos
        if (!$validador->estaLiberado()) {

            // Erro de validação
            throw new CamposObrigatoriosInvalidosException(
                'Dados informados são inválidos', 
                $validador->pegarErros()
            );

        }*/

        $criterio = array();

        if ($dados['titulo'] != '') {
            array_push($criterio, array('titulo', 'LIKE', '%' . $dados['titulo'] . '%'));
        }

        if ($dados['titulo_original'] != '') {
            array_push($criterio, array('titulo_original', 'LIKE', '%' . $dados['titulo_original'] . '%'));
        }

        if ($dados['idioma'] != '') {
            array_push($criterio, array('idioma', '=', $dados['idioma']));
        }

        // Monto critérios da pesquisa TORNAR DINAMICO
        /*$criterio = [
            ['cod_ddd_origem', '=', $dados['cod_ddd_origem']],
            ['cod_ddd_destino', '=', $dados['cod_ddd_destino']]
        ];*/

        // Pesquisa por livros utilizando critério informado
        $livros = $this->PesquisarLivrosAction->execute($criterio);

        // Retorno
        // Em caso de necessidade de paginação, caso esteja usando apenas collection, os dados são retornados sem informações de paginação (total de paginas, página atual, etc). 
        // Para incluir informações da paginação na resposta usamos getData() junto da collection.
        return (new LivroCollection($livros))->response()->getData(true);

    }



}
