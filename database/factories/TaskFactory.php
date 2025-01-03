<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $tasks = [
            'Planejar lançamento do produto digital',
            'Desenvolver landing page para o lançamento',
            'Criar campanha de email marketing',
            'Definir preço e pacotes do produto digital',
            'Gravar vídeo de lançamento',
            'Escrever página de vendas',
            'Desenvolver estratégia de SEO para o lançamento',
            'Configurar funil de vendas',
            'Criar conteúdo para redes sociais sobre o lançamento',
            'Configurar plataforma de pagamento',
            'Ajustar o design da página de vendas',
            'Criar material gráfico para anúncios',
            'Estabelecer metas de vendas',
            'Planejar cronograma de lançamentos',
            'Definir parcerias para o lançamento',
            'Desenvolver estratégias de marketing de afiliados',
            'Escrever copy para anúncios de Facebook',
            'Ajustar integração com CRM',
            'Fazer teste A/B no funil de vendas',
            'Criar scripts de vendas para equipe',
            'Criar conteúdo para blog sobre o lançamento',
            'Configurar campanhas de remarketing',
            'Realizar webinar de lançamento',
            'Planejar evento ao vivo para o lançamento',
            'Criar vídeos promocionais',
            'Desenvolver estratégia de marketing de influência',
            'Criar materiais para treinamento de afiliados',
            'Realizar pesquisa de mercado antes do lançamento',
            'Estabelecer parcerias estratégicas',
            'Revisar estratégia de preços',
            'Criar estratégia de marketing de conteúdo',
            'Testar a funcionalidade da plataforma de vendas',
            'Desenvolver campanha de anúncios pagos',
            'Ajustar o processo de checkout',
            'Criar estratégias para captação de leads',
            'Configurar integração com plataformas de e-mail',
            'Ajustar o sistema de notificações de lançamento',
            'Criar plano de marketing para influenciadores',
            'Desenvolver uma sequência de emails para o lançamento',
            'Ajustar fluxo de pagamento para afiliados',
            'Criar bônus exclusivos para o lançamento',
            'Planejar e executar campanhas de pré-venda',
            'Criar materiais de apoio para afiliados',
            'Desenvolver um guia para lançamento',
            'Analisar concorrência para o lançamento',
            'Ajustar estratégia de preço com base no feedback',
            'Planejar pós-lançamento e engajamento',
            'Desenvolver estratégias para aumentar as vendas',
            'Realizar testes de usabilidade na página de vendas',
            'Criar cronograma de publicação de conteúdos',
        ];

        return [
            'module_id' => Module::inRandomOrder()->first()->id,
            'status_id' => 1,
            'date_start' => fake()->dateTimeBetween('-1 week', '+1 month'),
            'name' => $tasks[array_rand($tasks)],
            'description' => fake()->paragraph,
            'created_by' => 1,
        ];

    }
}
