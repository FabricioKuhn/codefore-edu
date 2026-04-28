Documentação de Desenvolvimento: Sistema Educacional (Codeforce)

1. Visão Geral do Projeto
   Um sistema de gestão de aprendizagem (LMS) focado em gamificação, onde administradores e professores gerenciam turmas, alunos, aulas e atividades. O sistema utiliza pontos de experiência (XP) e controle de frequência.

Stack Tecnológica:

Backend: PHP 8.2.12 / Laravel 12.56.0

Frontend: Vue.js 3 / Inertia.js

Estilização: Tailwind CSS

Bundler: Vite

Banco de Dados: MySQL

2. Padrões de UI/UX e Design
   Para manter a consistência visual em todo o sistema, os seguintes padrões devem ser seguidos:

Alinhamento Macro: As páginas de listagem (Index) e detalhes (Show) devem ter um container com largura máxima de max-w-[1600px], centralizado com mx-auto.

Formulários: Devem ser contidos em max-w-4xl e centralizados (mx-auto) para melhor legibilidade, enquanto o cabeçalho (breadcrumb e título) permanece alinhado às margens da tela cheia.

Feedback Visual de Status:

Ativo: Badges verdes, opacidade 100%.

Inativo: Badges cinzas, opacidade reduzida (opacity-50) e filtro de escala de cinza (grayscale-[0.5]) na linha ou card.

Breadcrumbs: Devem seguir o padrão: Home > Turmas > [Ação/Nome], em letras maiúsculas, com fonte pequena (text-[10px]) e negrito.

3. Estrutura do Banco de Dados (Entidades Principais)
   Classrooms (Turmas)
   id, institution_id, teacher_id

name, subject, join_code

base_xp_level, level_growth_factor

total_lessons, start_date, frequency, days_of_week (JSON)

start_time, end_time, skip_holidays (Boolean)

is_active (Boolean - Default: true)

Lessons (Aulas)
id, classroom_id

title, content (Registro da aula), date, start_time, end_time

status (scheduled, recorded, canceled)

is_active (Boolean - sincronizado com a Turma)

xp_reward

Activities (Atividades)
id, classroom_id, lesson_id

title, description, points (XP)

end_date (Data de vencimento)

is_active (Boolean - sincronizado com a Turma)

4. Lógica de Negócio e Backend
   Inativação em Cascata (Soft Control)
   O sistema não utiliza exclusão física para Turmas. Ao "deletar" uma turma, o método destroy no ClassroomController alterna o status is_active.

Comportamento: Quando uma turma é inativada, todas as suas aulas (lessons) e atividades (activities) vinculadas também recebem is_active = false.

Reativação: O processo é reversível; ao reativar a turma, todos os conteúdos filhos são reativados.

Código de Referência (Controller):

PHP
public function destroy(Classroom $classroom) {
    $novoStatus = !$classroom->is_active;
$classroom->update(['is_active' => $novoStatus]);
$classroom->lessons()->update(['is_active' => $novoStatus]);
$classroom->activities()->update(['is_active' => $novoStatus]);
} 5. Componentes Frontend (Vue 3)
Modal.vue (Base)
Componente customizado para janelas sobrepostas, utilizando Teleport e Transition.

Props: show (Boolean), maxWidth (String), closeable (Boolean).

Hooks: Utiliza onMounted e onUnmounted para controle de scroll do body.

Show.vue (Gestão de Turma)
Organizado em um sistema de abas (Tabs):

Alunos: Lista com cálculo de frequência em tempo real e barra de progresso.

Aulas: Listagem de cronograma com modais para Chamada, Configuração LMS e Cancelamento.

Tarefas: Lista de atividades com indicadores de entregas pendentes (animate-pulse).

6. Problemas Solucionados (Knowledge Base para o Gem)
   Erro de Template Vue: O Vue exige que o conteúdo do <template> tenha apenas um elemento raiz ou use fragmentos corretamente. Erros de <template> geralmente travam a renderização.

BadMethodCallException (Request): No Laravel, não se deve chamar métodos inexistentes como $request->teacher(). Deve-se usar os dados validados do formulário: $validated['teacher_id'].

Sintaxe de Import no Vite: Utilizar o alias @ (ex: @/Components/Modal.vue) é preferível a caminhos relativos longos para evitar erros de resolução de módulos.

SyntaxError no Vue Hooks: Atenção aos nomes dos ciclos de vida: onUnmounted (correto) vs onUnformatted (incorreto). Erros de importação em componentes base travam todas as telas que os utilizam.

Formatação de Datas em Inputs: Inputs do tipo date no HTML esperam o formato YYYY-MM-DD. Ao carregar dados existentes, use .split('T')[0] em datas ISO do banco de dados.
