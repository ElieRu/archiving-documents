<script>
export default {
    data() {
        return {
            search: "",
        };
    },
    watch: {
        search() {
            this.$inertia.get(
                "/agents", {
                    search: this.search,
                }, {
                    preserveState: true,
                    replace: true,
                }
            );
        },
    },
    methods: {
        sortPoste (poste) {
            this.$inertia.get(
                "/agents", {
                    sort: poste,
                }, {
                    preserveState: true,
                    replace: true,
                }
            );
        }
    }
};
</script>

<template>
    <div
        id="dataTable_filter-1"
        class="text-md-end d-flex justify-content-end dataTables_filter"
    >
        <div class="bg-white border rounded d-flex align-items-center p-1">
            <input
                class="border-0 shadow-none form-control form-control-sm"
                type="search"
                @input="searching"
                aria-controls="dataTable"
                placeholder="Recherche"
                v-model="search"
            />
            <div class="dropdown">
                <button
                    class="btn btn-primary btn-sm border-0"
                    aria-expanded="false"
                    data-bs-toggle="dropdown"
                    type="button"
                    style="height: 29px"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512"
                        width="1em"
                        height="1em"
                        fill="currentColor"
                    >
                        <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) Copyright 2023 Fonticons, Inc. -->
                        <path
                            d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"
                        ></path>
                    </svg>
                </button>
                <div class="dropdown-menu">
                    <h6 class="dropdown-header">Poste</h6>
                    <a class="dropdown-item" style="cursor: pointer;" @click="sortPoste('')">Tous récupéré</a>
                    <a class="dropdown-item" style="cursor: pointer;" @click="sortPoste('caissier')">Caissiers</a>
                    <a class="dropdown-item" style="cursor: pointer;" @click="sortPoste('agent')">Agents</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>
