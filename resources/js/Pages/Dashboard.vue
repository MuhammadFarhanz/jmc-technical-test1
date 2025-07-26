<script setup>
import KabupatenTable from "@/Components/Table/KabupatenTable.vue";
import PendudukTable from "@/Components/Table/PendudukTable.vue";
import ProvinsiTable from "@/Components/Table/ProvinsiTable.vue";
import ReportSection from "@/Components/Table/ReportSection.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { usePage } from "@inertiajs/vue3";

const { kabupatens, provinsis, penduduks } = usePage().props;
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-8 m-10 p-10 max-w-7xl mx-auto">
            <section>
                <h2 class="text-xl font-bold mb-2">Daftar Provinsi</h2>
                <ProvinsiTable :provinsis="provinsis" />
            </section>

            <section>
                <h2 class="text-xl font-bold mb-2">Daftar Kabupaten</h2>
                <KabupatenTable
                    :kabupatens="kabupatens"
                    :provinsis="provinsis"
                />
            </section>

            <section>
                <h2 class="text-xl font-bold mb-2">Daftar Penduduk</h2>
                <PendudukTable
                    :penduduks="penduduks"
                    :provinsis="provinsis"
                    :kabupatens="kabupatens"
                />
            </section>
            <ReportSection :provinsis="provinsis" :kabupatens="kabupatens" />
        </div>
    </AuthenticatedLayout>
</template>
