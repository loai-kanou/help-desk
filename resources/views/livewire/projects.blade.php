<div class="w-full flex flex-col justify-start items-start gap-5">
    <div class="w-full flex md:flex-row flex-col justify-between items-start gap-2">
        <div class="flex flex-col justify-center items-start gap-1">
            <span class="lg:text-4xl md:text-2xl text-xl font-medium text-gray-700">
                @lang('Projects')
            </span>
            <span class="lg:text-lg md:text-sm text-xs font-light text-gray-500">
                @lang('Below is the list of configured projects in :app', [
                    'app' => config('app.name')
                ])
            </span>
        </div>
        @if(has_all_permissions(auth()->user(), 'create-projects'))
            <button type="button" wire:click="createProject()" class="bg-primary-700 text-white hover:bg-primary-800 px-4 py-2 rounded-lg shadow hover:shadow-lg text-base">
                @lang('Create a new project')
            </button>
        @endif
    </div>
    @if(auth()->user()->favoriteProjects()->count())
        <div class="w-full mt-5 flex flex-col justify-start items-start gap-2">
            <span class="text-lg text-warning-500 font-medium flex flex-row justify-start items-center gap-2">
                <i class="fa fa-star"></i>
                @lang('Favorite projects')
            </span>
            <div class="w-full flex flex-row justify-start items-start flex-wrap -ml-2">
                @foreach(auth()->user()->favoriteProjects as $project)
                    <div class="xl:w-1/5 lg:w-1/4 md:w-1/3 sm:w-1/2 w-full p-2">
                        <div class="w-full flex flex-col gap-1 p-5 border border-gray-100 rounded-lg shadow bg-white hover:shadow-lg">
                            <span class="text-gray-700 font-bold text-base">
                                {{ $project->name }}
                            </span>
                            <span class="text-gray-500 font-light text-sm" style="min-height: 120px;">
                                {{ Str::limit(htmlspecialchars(strip_tags($project->description)), 100) }}
                            </span>
                            <span class="text-warning-500 font-medium text-xs flex flex-row items-center gap-1">
                                <i class="fa fa-ticket"></i> {{ $project->tickets()->count() }} @lang($project->tickets()->count() > 1 ? 'Tickets' : 'Ticket')
                            </span>
                            <a href="{{ route('tickets', ['project' => $project->id]) }}" class="mt-2 text-primary-500 hover:text-primary-600 font-normal text-sm hover:underline">
                                @lang('View tickets')
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <div class="w-full mt-5">
        <div class="w-full flex flex-col justify-start items-start gap-5">
            <div class="w-full overflow-x-auto relative sm:rounded-lg">
                {{ $this->table }}
            </div>

            <div id="projectModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex items-center justify-center w-full md:inset-0 h-modal md:h-full">
                <div class="relative p-4 w-full max-w-4xl h-full md:h-auto">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex justify-between items-start p-4 rounded-t border-b dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                @lang($selectedProject?->id ? 'Update project' : 'Create a new project')
                            </h3>
                            <button wire:click="cancelProject()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                        @if($selectedProject)
                            @livewire('projects-dialog', ['project' => $selectedProject])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="hidden" data-modal-toggle="projectModal" id="toggleProjectModal"></button>

    @push('scripts')
        <script>
            window.addEventListener('toggleProjectModal', () => {
                const toggleProjectModalBtn = document.querySelector('#toggleProjectModal');
                toggleProjectModalBtn.click();
            });
        </script>
    @endpush
</div>
