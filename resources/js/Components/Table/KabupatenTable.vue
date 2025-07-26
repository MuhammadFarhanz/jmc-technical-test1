<script setup>
import { ref, computed, watch } from "vue";
import { router, useForm } from "@inertiajs/vue3";

const { kabupatens: propKabupatens, provinsis: propProvinsis } = defineProps({
    kabupatens: Array,
    provinsis: Array,
});

const kabupatens = ref([...propKabupatens]);
const provinsis = ref([...propProvinsis]);

watch(
    () => propKabupatens,
    (newVal) => {
        kabupatens.value = [...newVal];
    },
    { deep: true }
);

watch(
    () => propProvinsis,
    (newVal) => {
        provinsis.value = [...newVal];
    },
    { deep: true }
);

// States
const search = ref("");
const selectedProvinsi = ref("");
const showAddDialog = ref(false);
const showEditDialog = ref(false);
const editingKabupaten = ref(null);

// Forms
const addForm = useForm({
    nama: "",
    jumlah_penduduk: 0,
    provinsi_id: "",
});

const editForm = useForm({
    id: null,
    nama: "",
    jumlah_penduduk: 0,
    provinsi_id: "",
});

const filtered = computed(() => {
    const searchTerm = search.value.toLowerCase();
    const provinsiFilter = selectedProvinsi.value;

    return kabupatens.value.filter((kab) => {
        const matchesSearch = kab.nama.toLowerCase().includes(searchTerm);
        const matchesProvinsi =
            !provinsiFilter || kab.provinsi_id == provinsiFilter;
        return matchesSearch && matchesProvinsi;
    });
});

function handleSaveKabupaten() {
    if (!addForm.nama.trim() || !addForm.provinsi_id) return;

    addForm.post("/kabupaten", {
        preserveScroll: true,
        onSuccess: (res) => {
            kabupatens.value.push({
                ...addForm.data(),
                id: res.props.last_id,
                provinsi: provinsis.value.find(
                    (p) => p.id === addForm.provinsi_id
                ),
            });
            addForm.reset();
            showAddDialog.value = false;
        },
    });
}

function openEditDialog(kabupaten) {
    editingKabupaten.value = kabupaten;
    editForm.id = kabupaten.id;
    editForm.nama = kabupaten.nama;
    editForm.jumlah_penduduk = kabupaten.jumlah_penduduk;
    editForm.provinsi_id = kabupaten.provinsi_id;
    showEditDialog.value = true;
}
function handleUpdate() {
    if (!editForm.id) {
        console.error("No ID available for update");
        return;
    }

    editForm.put(route("kabupaten.update", editForm.id), {
        preserveScroll: true,
        onSuccess: (res) => {
            const index = kabupatens.value.findIndex(
                (k) => k.id === editForm.id
            );
            if (index !== -1) {
                kabupatens.value[index] = {
                    ...kabupatens.value[index],
                    ...editForm.data(),
                    provinsi: provinsis.value.find(
                        (p) => p.id === editForm.provinsi_id
                    ),
                };
            }
            editForm.reset();
            showEditDialog.value = false;
        },
    });
}

function handleDeleteKabupaten(id) {
    if (confirm("Apakah Anda yakin ingin menghapus?")) {
        router.delete(route("kabupaten.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                kabupatens.value = kabupatens.value.filter((k) => k.id !== id);
            },
        });
    }
}
</script>

<template>
    <div class="flex justify-between items-center mb-4 gap-2">
        <button
            class="bg-green-500 text-white px-4 py-2 rounded"
            @click="showAddDialog = true"
        >
            + Tambah
        </button>
        <!-- Modal Tambah Kabupaten -->
        <div
            v-if="showAddDialog"
            class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 space-y-4 rounded w-80 shadow-lg">
                <h2 class="text-lg font-bold mb-4">Tambah Kabupaten</h2>

                <label class="block mb-2 text-sm font-medium"
                    >Nama Kabupaten</label
                >
                <input
                    v-model="addForm.nama"
                    class="w-full border px-3 py-2 rounded mb-1"
                    placeholder="Masukkan nama Kabupaten"
                />

                <label class="block mb-1 text-sm font-medium"
                    >Jumlah Penduduk</label
                >
                <input
                    v-model.number="addForm.jumlah_penduduk"
                    type="number"
                    class="w-full border px-3 py-2 rounded mb-2"
                    placeholder="Masukkan jumlah penduduk"
                />

                <label class="block mb-1">Pilih Provinsi</label>
                <select
                    v-model="addForm.provinsi_id"
                    class="w-full border px-3 py-2 rounded mb-1"
                >
                    <option value="">-- Pilih Provinsi --</option>
                    <option
                        v-for="prov in provinsis"
                        :key="prov.id"
                        :value="prov.id"
                    >
                        {{ prov.nama }}
                    </option>
                </select>

                <div class="flex justify-end gap-2 mt-4">
                    <button
                        class="px-4 py-2 rounded border"
                        @click="showAddDialog = false"
                    >
                        Batal
                    </button>
                    <button
                        class="bg-green-600 text-white px-4 py-2 rounded"
                        @click="handleSaveKabupaten"
                        :disabled="addForm.processing"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </div>

        <div>
            <!-- Filter Provinsi -->
            <select v-model="selectedProvinsi" class="border mr-2 py-1 rounded">
                <option value="">Semua Provinsi</option>
                <option
                    v-for="prov in provinsis"
                    :key="prov.id"
                    :value="prov.id"
                >
                    {{ prov.nama }}
                </option>
            </select>

            <!-- Search -->
            <input
                v-model="search"
                class="border px-3 py-1 rounded"
                placeholder="Cari Kabupaten..."
            />
        </div>
    </div>

    <!-- Edit Dialog -->
    <div
        v-if="showEditDialog"
        class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 space-y-4 rounded w-80 shadow-lg">
            <h2 class="text-lg font-bold mb-4">Edit Kabupaten</h2>

            <div>
                <label class="block mb-2 text-sm font-medium"
                    >Nama Kabupaten</label
                >
                <input
                    v-model="editForm.nama"
                    class="w-full border px-3 py-2 rounded mb-1"
                />
                <p v-if="editForm.errors.nama" class="text-red-500 text-sm">
                    {{ editForm.errors.nama }}
                </p>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium"
                    >Jumlah Penduduk</label
                >
                <input
                    v-model.number="editForm.jumlah_penduduk"
                    type="number"
                    class="w-full border px-3 py-2 rounded mb-2"
                />
            </div>

            <div>
                <label class="block mb-1">Provinsi</label>
                <select
                    v-model="editForm.provinsi_id"
                    class="w-full border px-3 py-2 rounded mb-1"
                >
                    <option
                        v-for="prov in provinsis"
                        :key="prov.id"
                        :value="prov.id"
                    >
                        {{ prov.nama }}
                    </option>
                </select>
                <p
                    v-if="editForm.errors.provinsi_id"
                    class="text-red-500 text-sm"
                >
                    {{ editForm.errors.provinsi_id }}
                </p>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button
                    class="px-4 py-2 rounded border"
                    @click="showEditDialog = false"
                >
                    Batal
                </button>
                <button
                    class="bg-blue-600 text-white px-4 py-2 rounded"
                    @click="handleUpdate"
                    :disabled="editForm.processing"
                >
                    <span v-if="editForm.processing">Mengupdate...</span>
                    <span v-else>Update</span>
                </button>
            </div>
        </div>
    </div>

    <table class="w-full border text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Provinsi</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="kab in filtered" :key="kab.id">
                <td class="border px-4 py-2">{{ kab.nama }}</td>
                <td class="border px-4 py-2">
                    {{ kab.provinsi?.nama ?? "-" }}
                </td>
                <td class="border px-4 py-2">
                    <button
                        class="text-blue-500 mr-2"
                        @click="openEditDialog(kab)"
                    >
                        Edit
                    </button>
                    <button
                        class="text-red-500"
                        @click="handleDeleteKabupaten(kab.id)"
                    >
                        Hapus
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</template>
