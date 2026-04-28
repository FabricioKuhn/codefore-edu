<template>
    <AppLayout>
        <Head :title="`Turma: ${classroom.name}`" />

        <div class="py-6 min-h-screen">
            <div class="w-full max-w-[1600px] mx-auto px-5 sm:px-6 lg:px-8">
                <div
                    class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4"
                >
                    <div>
                        <nav
                            class="flex text-gray-400 font-bold text-[10px] uppercase tracking-widest mb-1"
                        >
                            <ol
                                class="inline-flex items-center space-x-1 md:space-x-2"
                            >
                                <li>
                                    <Link
                                        :href="route(user.role + '.dashboard')"
                                        class="hover:text-primary transition-colors"
                                        >Home</Link
                                    >
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg
                                            class="w-3 h-3 text-gray-300 mx-1"
                                            fill="none"
                                            viewBox="0 0 6 10"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 9 4-4-4-4"
                                            />
                                        </svg>
                                        <Link
                                            :href="
                                                route(
                                                    user.role +
                                                        '.classrooms.index',
                                                )
                                            "
                                            class="hover:text-primary transition-colors"
                                            >Turmas</Link
                                        >
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg
                                            class="w-3 h-3 text-gray-300 mx-1"
                                            fill="none"
                                            viewBox="0 0 6 10"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m1 9 4-4-4-4"
                                            />
                                        </svg>
                                        <span class="text-gray-500"
                                            >Gerenciar</span
                                        >
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h2
                            class="font-bold text-2xl text-secondary leading-tight flex items-center gap-3"
                        >
                            {{ classroom.name }}
                            <span class="text-gray-400 font-normal"
                                >| {{ classroom.subject }}</span
                            >
                        </h2>
                    </div>

                    <div class="flex gap-3">
                        <button
                            @click="showEnrollModal = true"
                            class="px-5 py-2.5 bg-white border border-gray-200 text-secondary rounded-xl font-bold text-[11px] uppercase tracking-widest hover:bg-gray-50 transition-all shadow-sm"
                        >
                            <i class="fas fa-user-plus mr-2 text-primary"></i>
                            Matricular Aluno
                        </button>
                        <Link
                            :href="
                                route(user.role + '.activities.create', {
                                    classroom_id: classroom.id,
                                })
                            "
                            class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-[11px] uppercase tracking-widest hover:opacity-90 transition-all shadow-sm shadow-primary/20"
                        >
                            <i class="fas fa-plus mr-2"></i> Nova Atividade
                        </Link>
                    </div>
                </div>

                <div
                    class="flex border-b border-gray-100 mb-6 bg-white rounded-t-2xl px-4 pt-2 shadow-sm overflow-x-auto whitespace-nowrap"
                >
                    <button
                        @click="activeTab = 'students'"
                        :class="
                            activeTab === 'students'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-400'
                        "
                        class="px-6 py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all"
                    >
                        Alunos
                    </button>
                    <button
                        @click="activeTab = 'lessons'"
                        :class="
                            activeTab === 'lessons'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-400'
                        "
                        class="px-6 py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all"
                    >
                        Aulas
                    </button>
                    <button
                        @click="activeTab = 'tasks'"
                        :class="
                            activeTab === 'tasks'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-gray-400'
                        "
                        class="px-6 py-4 border-b-2 font-black text-[10px] uppercase tracking-widest transition-all"
                    >
                        Tarefas / Atividades
                    </button>
                </div>

                <div
                    class="bg-white rounded-b-2xl shadow-sm border border-gray-100 overflow-hidden mb-12"
                >
                    <div
                        v-if="activeTab === 'students'"
                        class="overflow-x-auto"
                    >
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Nome do Aluno
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center"
                                    >
                                        Aulas (P/T)
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Frequência
                                    </th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        XP Total
                                    </th>
                                    <th
                                        class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest"
                                    >
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="student in classroom.students"
                                    :key="student.id"
                                    class="hover:bg-gray-50/30 transition"
                                >
                                    <td
                                        class="px-6 py-4 font-bold text-secondary text-sm"
                                    >
                                        {{ student.name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center text-xs font-bold text-gray-500"
                                    >
                                        {{ student.pivot?.present_count || 0 }}
                                        / {{ lessons.length }}
                                    </td>
                                    <td class="px-6 py-4 w-64">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden"
                                            >
                                                <div
                                                    class="bg-primary h-full transition-all duration-700"
                                                    :style="`width: ${calculateFreq(student)}%`"
                                                ></div>
                                            </div>
                                            <span
                                                class="text-[10px] font-black text-secondary"
                                                >{{
                                                    calculateFreq(student)
                                                }}%</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 font-black text-xs text-primary"
                                    >
                                        {{ student.pivot?.xp_earned || 0 }} XP
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button
                                            class="text-[10px] font-black uppercase text-red-300 hover:text-red-500 transition-colors"
                                        >
                                            Remover
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'lessons'" class="overflow-x-auto">
                        <div class="px-6 py-4 flex justify-between items-center bg-gray-50/30 border-b border-gray-100">
        <h3 class="text-[11px] font-black text-secondary uppercase tracking-widest">Cronograma de Aulas</h3>
        <button 
            @click="openAddLessonModal"
            class="px-4 py-2 bg-white border border-primary/20 text-primary rounded-lg font-bold text-[10px] uppercase hover:bg-primary hover:text-white transition-all shadow-sm"
        >
            <i class="fas fa-calendar-plus mr-2"></i> Adicionar Aula
        </button>
    </div>
                        <table class="w-full text-left">
                            <thead
                                class="bg-gray-50/50 text-[10px] text-gray-400 font-black uppercase tracking-widest"
                            >
                                <tr>
                                    <th class="px-6 py-4">#</th>
                                    <th class="px-6 py-4">Título</th>
                                    <th class="px-6 py-4">Data / Hora</th>
                                    <th class="px-6 py-4 text-center">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-center">
                                        Presenças
                                    </th>
                                    <th class="px-6 py-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="(lesson, idx) in lessons"
                                    :key="lesson.id"
                                    class="hover:bg-gray-50/30 transition"
                                >
                                    <td
                                        class="px-6 py-4 text-xs font-black text-gray-300"
                                    >
                                        {{ idx + 1 }}
                                    </td>
                                    <td
                                        class="px-6 py-4 font-bold text-secondary text-sm"
                                    >
                                        {{ lesson.title || "Aula Agendada" }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-bold text-secondary">
                                            {{ formatDate(lesson.date) }}
                                        </div>
                                        <div
                                            class="text-[9px] font-black text-gray-400 uppercase"
                                        >
                                            {{
                                                lesson.start_time.substring(
                                                    0,
                                                    5,
                                                )
                                            }}
                                            -
                                            {{
                                                lesson.end_time.substring(0, 5)
                                            }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            :class="statusClass(lesson.status)"
                                            class="px-2 py-1 rounded text-[9px] font-black uppercase tracking-widest"
                                        >
                                            {{ lesson.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-center font-bold text-secondary text-xs"
                                    >
                                        {{ lesson.attendances_count || 0 }} /
                                        {{ classroom.students.length }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right whitespace-nowrap"
                                    >
                                        <button
                                            v-if="lesson.status !== 'canceled'"
                                            @click="openAttendance(lesson)"
                                            class="text-primary font-black text-[10px] uppercase mr-4"
                                        >
                                            Chamada
                                        </button>
                                        <button
                                            v-if="lesson.status !== 'canceled'"
                                            @click="openConfig(lesson)"
                                            class="text-secondary font-black text-[10px] uppercase mr-4"
                                        >
                                            Configurar
                                        </button>
                                        <button
                                            v-if="lesson.status === 'scheduled'"
                                            @click="openCancel(lesson)"
                                            class="text-red-400 font-black text-[10px] uppercase"
                                        >
                                            Cancelar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="activeTab === 'tasks'" class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead
                                class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest"
                            >
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Atividade</th>
                                    <th class="px-6 py-4 text-center">
                                        Entregas Pendentes
                                    </th>
                                    <th class="px-6 py-4 text-center">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr
                                    v-for="activity in activities"
                                    :key="activity.id"
                                    class="hover:bg-gray-50/30 transition"
                                >
                                    <td
                                        class="px-6 py-4 text-xs font-black text-gray-300"
                                    >
                                        #{{ activity.id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="font-bold text-secondary block"
                                            >{{ activity.title }}</span
                                        >
                                        <span
                                            class="text-[9px] font-black text-primary uppercase"
                                            >{{ activity.points || 0 }} XP</span
                                        >
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div
                                            v-if="
                                                activity.pending_submissions > 0
                                            "
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 text-yellow-700 rounded-lg text-xs font-bold animate-pulse"
                                        >
                                            {{ activity.pending_submissions }}
                                            avaliar
                                        </div>
                                        <span v-else class="text-gray-300"
                                            >-</span
                                        >
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[9px] font-black uppercase"
                                            >{{ activity.status }}</span
                                        >
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link
                                            :href="
                                                route(
                                                    user.role +
                                                        '.activities.show',
                                                    activity.id,
                                                )
                                            "
                                            class="text-primary font-black text-[10px] uppercase"
                                            >Gerenciar</Link
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal
            :show="showEnrollModal"
            @close="showEnrollModal = false"
            max-width="md"
        >
            <div class="p-8">
                <h3 class="text-xl font-bold text-secondary mb-6 text-center">
                    Matricular Aluno
                </h3>
                <form @submit.prevent="enrollStudent">
                    <div class="mb-6">
                        <label
                            class="block text-xs font-black uppercase text-gray-400 mb-2"
                            >Selecione o Aluno</label
                        >
                        <select
                            v-model="enrollForm.student_id"
                            required
                            class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl text-sm shadow-sm"
                        >
                            <option value="" disabled>
                                Escolha um aluno disponível...
                            </option>
                            <option
                                v-for="s in availableStudents"
                                :key="s.id"
                                :value="s.id"
                            >
                                {{ s.name }}
                            </option>
                        </select>
                    </div>
                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showEnrollModal = false"
                            class="flex-1 py-3 text-[11px] font-black uppercase text-gray-400"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="flex-1 py-3 bg-primary text-white rounded-xl font-black text-[11px] uppercase shadow-lg shadow-primary/20"
                        >
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal
            :show="showAttendanceModal"
            @close="showAttendanceModal = false"
            max-width="2xl"
        >
            <div class="bg-primary px-8 py-4">
                <h3
                    class="text-lg font-black text-secondary uppercase tracking-tight"
                >
                    Chamada: {{ selectedLesson?.title }}
                </h3>
            </div>
            <form @submit.prevent="saveAttendance" class="p-6">
                <div class="max-h-[50vh] overflow-y-auto mb-6">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="student in classroom.students"
                                :key="student.id"
                            >
                                <td
                                    class="px-4 py-4 text-sm font-bold text-secondary"
                                >
                                    {{ student.name }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-6">
                                        <label
                                            class="flex items-center gap-2 cursor-pointer group"
                                        >
                                            <input
                                                type="radio"
                                                value="present"
                                                v-model="
                                                    attendanceForm.students[
                                                        student.id
                                                    ]
                                                "
                                                class="text-primary focus:ring-primary"
                                            />
                                            <span
                                                class="text-[10px] font-black uppercase text-gray-400 group-hover:text-primary"
                                                >Presente</span
                                            >
                                        </label>
                                        <label
                                            class="flex items-center gap-2 cursor-pointer group"
                                        >
                                            <input
                                                type="radio"
                                                value="absent"
                                                v-model="
                                                    attendanceForm.students[
                                                        student.id
                                                    ]
                                                "
                                                class="text-red-400 focus:ring-red-400"
                                            />
                                            <span
                                                class="text-[10px] font-black uppercase text-gray-400 group-hover:text-red-400"
                                                >Falta</span
                                            >
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end gap-3 border-t pt-4">
                    <button
                        type="button"
                        @click="showAttendanceModal = false"
                        class="px-6 py-2 text-[11px] font-black text-gray-400 uppercase"
                    >
                        Voltar
                    </button>
                    <button
                        type="submit"
                        class="px-8 py-2 bg-primary text-white rounded-xl font-bold text-[11px] uppercase"
                    >
                        Salvar Chamada
                    </button>
                </div>
            </form>
        </Modal>

        <Modal
            :show="showConfigModal"
            @close="showConfigModal = false"
            max-width="3xl"
        >
            <div
                class="bg-secondary px-8 py-4 flex justify-between items-center"
            >
                <h3
                    class="text-lg font-black text-white uppercase tracking-tight"
                >
                    Configurar Aula:
                    {{ selectedLesson?.title || "Aula Agendada" }}
                </h3>
                <button
                    @click="showConfigModal = false"
                    class="text-white/50 hover:text-white transition-colors"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form
                @submit.prevent="saveConfig"
                class="p-8 space-y-6 max-h-[75vh] overflow-y-auto"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-gray-400 mb-1"
                                >Título da Aula *</label
                            >
                            <input
                                v-model="configForm.title"
                                type="text"
                                required
                                class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                placeholder="Ex: Introdução à Lógica"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-gray-400 mb-1"
                                >Conteúdo Ministrado / Resumo</label
                            >
                            <textarea
                                v-model="configForm.content"
                                rows="6"
                                class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                placeholder="Descreva o que foi ensinado..."
                            ></textarea>
                        </div>
                    </div>

                    <div
                        class="space-y-4 bg-gray-50/50 p-5 rounded-2xl border border-gray-100"
                    >
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-gray-400 mb-1"
                                >Vídeo (YouTube/Vimeo)</label
                            >
                            <input
                                v-model="configForm.video_url"
                                type="url"
                                class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm"
                                placeholder="https://youtube.com/..."
                            />
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black uppercase text-gray-400 mb-1"
                                >Material de Apoio (PDF)</label
                            >
                            <input
                                type="file"
                                @input="
                                    configForm.material = $event.target.files[0]
                                "
                                class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all cursor-pointer"
                            />
                        </div>
                        <div class="pt-2 border-t border-gray-200">
                            <label
                                class="block text-[10px] font-black uppercase text-gray-400 mb-1"
                                >Recompensa (XP)</label
                            >
                            <div class="flex items-center gap-3">
                                <input
                                    v-model="configForm.xp_reward"
                                    type="number"
                                    class="w-24 border-gray-200 focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm font-black text-primary"
                                />
                                <span
                                    class="text-[10px] text-gray-400 font-bold uppercase"
                                    >XP ao concluir</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label
                        class="block text-[10px] font-black uppercase text-gray-400 mb-3"
                        >Vincular Tarefas a esta Aula</label
                    >
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label
                            v-for="act in activities"
                            :key="act.id"
                            class="flex items-center gap-3 p-3 bg-white border border-gray-100 rounded-xl cursor-pointer hover:border-primary/40 transition-all"
                        >
                            <input
                                type="checkbox"
                                :value="act.id"
                                v-model="configForm.activity_ids"
                                class="rounded border-gray-300 text-primary focus:ring-primary"
                            />
                            <div class="flex flex-col">
                                <span
                                    class="text-xs font-bold text-secondary truncate"
                                    >{{ act.title }}</span
                                >
                                <span
                                    class="text-[9px] font-black text-primary uppercase"
                                    >{{ act.points }} XP</span
                                >
                            </div>
                        </label>
                    </div>
                </div>

                <div
                    class="flex justify-end gap-3 pt-4 border-t border-gray-100"
                >
                    <button
                        type="button"
                        @click="showConfigModal = false"
                        class="px-6 py-2 text-[11px] font-black text-gray-400 uppercase tracking-widest"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="configForm.processing"
                        class="px-8 py-3 bg-secondary text-white rounded-xl font-bold text-[11px] uppercase tracking-widest shadow-lg shadow-secondary/20"
                    >
                        {{
                            configForm.processing
                                ? "Salvando..."
                                : "Salvar Configuração"
                        }}
                    </button>
                </div>
            </form>
        </Modal>

        <Modal
            :show="showCancelModal"
            @close="showCancelModal = false"
            max-width="md"
        >
            <div class="bg-red-500 p-6">
                <h3
                    class="text-lg font-black text-white uppercase tracking-tight"
                >
                    Cancelar Aula
                </h3>
            </div>
            <form @submit.prevent="cancelLesson" class="p-8">
                <label
                    class="block text-xs font-black uppercase text-gray-400 mb-2"
                    >Justificativa</label
                >
                <textarea
                    v-model="cancelForm.justification"
                    required
                    rows="4"
                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm mb-6"
                    placeholder="Motivo do cancelamento..."
                ></textarea>
                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="showCancelModal = false"
                        class="flex-1 py-3 text-[11px] font-black uppercase text-gray-400"
                    >
                        Voltar
                    </button>
                    <button
                        type="submit"
                        class="flex-1 py-3 bg-red-500 text-white rounded-xl font-black text-[11px] uppercase"
                    >
                        Confirmar
                    </button>
                </div>
            </form>
        </Modal>

        <Modal :show="showAddLessonModal" @close="showAddLessonModal = false" max-width="3xl">
    <div class="bg-white border-b border-gray-100 px-8 py-6 flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-secondary tracking-tight">
                Adicionar Aulas Manuais
            </h3>
            <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-1">
                Turma: {{ classroom.name }}
            </p>
        </div>
        <button 
            @click="showAddLessonModal = false" 
            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all"
        >
            <i class="fas fa-times"></i>
        </button>
    </div>

   <form @submit.prevent="submitAddLessons" class="p-8 bg-gray-50/30">
        <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2 mb-8 custom-scrollbar">
    <div 
        v-for="(item, index) in addLessonForm.lessons" 
        :key="index" 
        class="grid grid-cols-12 gap-4 p-5 bg-white rounded-2xl border border-gray-100 shadow-sm relative group hover:border-primary/30 transition-all"
    >
        <div class="col-span-12 md:col-span-5">
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1">Título da Aula</label>
            <input 
                v-model="item.title" 
                type="text" 
                placeholder="Ex: Aula Extra de Reforço" 
                class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl text-sm h-11" 
                required 
            />
        </div>

        <div class="col-span-6 md:col-span-3">
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1">Data</label>
            <input 
                v-model="item.date" 
                type="date" 
                class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl text-sm h-11" 
                required 
            />
        </div>

        <div class="col-span-3 md:col-span-2">
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1">Início</label>
            <input 
                v-model="item.start_time" 
                type="time" 
                class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl text-sm h-11 text-center" 
                required 
            />
        </div>

        <div class="col-span-3 md:col-span-2">
            <label class="block text-[10px] font-black text-gray-400 uppercase mb-1.5 ml-1">Fim</label>
            <input 
                v-model="item.end_time" 
                type="time" 
                class="w-full border-gray-200 focus:border-primary focus:ring-primary rounded-xl text-sm h-11 text-center" 
                required 
            />
        </div>

        <button 
            v-if="addLessonForm.lessons.length > 1" 
            type="button" 
            @click="removeLessonRow(index)"
            class="absolute -right-2 -top-2 w-7 h-7 bg-white border border-red-100 text-red-500 rounded-full text-xs shadow-md opacity-0 group-hover:opacity-100 transition-all hover:bg-red-500 hover:text-white"
        >
            <i class="fas fa-trash-alt"></i>
        </button>
    </div>
</div>
<div v-if="addLessonForm.errors.conflict" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs font-bold rounded-r-xl animate-shake">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    {{ addLessonForm.errors.conflict }}
</div>

<div class="flex justify-between items-center">
    </div>

        <div class="flex justify-between items-center">
            <button type="button" @click="addMoreLessonRow" 
                    class="text-primary font-black text-[11px] uppercase hover:underline">
                <i class="fas fa-plus mr-1"></i> Adicionar
            </button>

            <div class="flex gap-3">
                <button type="button" @click="showAddLessonModal = false" class="px-6 py-2 text-[11px] font-black text-gray-400 uppercase">
                    Cancelar
                </button>
                <button type="submit" :disabled="addLessonForm.processing"
                        class="px-8 py-3 bg-primary text-white rounded-xl font-bold text-[11px] uppercase shadow-lg shadow-primary/20">
                    {{ addLessonForm.processing ? 'Salvando...' : 'Salvar Aulas' }}
                </button>
            </div>
        </div>
        
        
    </form>
</Modal>


    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Modal from "@/Components/Modal.vue";
import { Head, Link, useForm, usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const route = window.route;
const props = defineProps({
    classroom: Object,
    lessons: Array,
    activities: Array,
    availableStudents: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const activeTab = ref("students");

// MODAIS
const showEnrollModal = ref(false);
const showAttendanceModal = ref(false);
const showCancelModal = ref(false);
const selectedLesson = ref(null);

// FORMS
const enrollForm = useForm({ student_id: "" });
const attendanceForm = useForm({ students: {} });
const cancelForm = useForm({ justification: "" });

// HELPERS
const calculateFreq = (student) => {
    const recorded = props.lessons.filter(
        (l) => l.status === "recorded",
    ).length;
    if (recorded === 0) return 100;
    return Math.round(((student.pivot?.present_count || 0) / recorded) * 100);
};

const formatDate = (d) => {
    if (!d) return "--/--/----";
    // Pegamos apenas os primeiros 10 caracteres (YYYY-MM-DD) para evitar problemas com horários vindo junto
    const cleanDate = d.toString().substring(0, 10);
    const [year, month, day] = cleanDate.split('-');
    return `${day}/${month}/${year}`;
};

// Adicione esta também para evitar o erro de "substring of null" que quebra o layout
const formatTime = (time) => {
    if (!time) return "--:--";
    return time.substring(0, 5);
};

const statusClass = (s) => {
    if (s === "scheduled") return "bg-blue-50 text-blue-500";
    if (s === "recorded") return "bg-green-50 text-green-500";
    if (s === "canceled") return "bg-red-50 text-red-400";
    return "bg-gray-50 text-gray-400";
};

// ACTIONS
const enrollStudent = () => {
    enrollForm.post(
        route(
            user.value.role + ".classrooms.students.store",
            props.classroom.id,
        ),
        {
            onSuccess: () => {
                showEnrollModal.value = false;
                enrollForm.reset();
            },
        },
    );
};

const openAttendance = (lesson) => {
    selectedLesson.value = lesson;
    props.classroom.students.forEach((s) => {
        attendanceForm.students[s.id] = "present";
    });
    showAttendanceModal.value = true;
};

const saveAttendance = () => {
    attendanceForm.post(
        route(user.value.role + ".lessons.attendance", selectedLesson.value.id),
        {
            onSuccess: () => {
                showAttendanceModal.value = false;
            },
        },
    );
};

const openCancel = (lesson) => {
    selectedLesson.value = lesson;
    showCancelModal.value = true;
};

const cancelLesson = () => {
    cancelForm.post(
        route(user.value.role + ".lessons.cancel", selectedLesson.value.id),
        {
            onSuccess: () => {
                showCancelModal.value = false;
                cancelForm.reset();
            },
        },
    );
};

const openConfig = (lesson) => {
    // Aqui você redirecionaria ou abriria o modal de LMS
    console.log("Configurar aula", lesson.id);
};

const showAddLessonModal = ref(false);

// Iniciamos o formulário com uma aula vazia
const addLessonForm = useForm({
    lessons: [
        { title: '', date: '', start_time: props.classroom.start_time, end_time: props.classroom.end_time }
    ]
});

const openAddLessonModal = () => {
    addLessonForm.reset();
    showAddLessonModal.value = true;
};

// Função para o botão "Adicionar +1"
const addMoreLessonRow = () => {
    addLessonForm.lessons.push({ 
        title: '', 
        date: '', 
        start_time: props.classroom.start_time, 
        end_time: props.classroom.end_time 
    });
};

const removeLessonRow = (index) => {
    if (addLessonForm.lessons.length > 1) {
        addLessonForm.lessons.splice(index, 1);
    }
};

const submitAddLessons = () => {
    addLessonForm.post(route(user.value.role + '.classrooms.lessons.add-manual', props.classroom.id), {
        onSuccess: () => {
            showAddLessonModal.value = false;
            addLessonForm.reset();
        },
    });
};
</script>
