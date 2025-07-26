<script setup>
import { ref, computed, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";

const {
    provinsis: propProvinsis,
    kabupatens: propKabupatens,
    penduduks: propPenduduks,
} = defineProps({
    provinsis: Array,
    kabupatens: Array,
    penduduks: Array,
});

const provinsis = ref([...propProvinsis]);
const kabupatens = ref([...propKabupatens]);
const penduduks = ref([...propPenduduks]);

watch(
    () => propPenduduks,
    (newVal) => {
        penduduks.value = [...newVal];
    },
    { deep: true }
);

const search = ref("");
const selectedProvinsi = ref("");
const selectedKabupaten = ref("");
const showAddDialog = ref(false);
const showEditDialog = ref(false);
const editingId = ref(null);

const addForm = useForm({
    nama: "",
    nik: "",
    umur: 0,
    alamat: "",
    provinsi_id: "",
    kabupaten_id: "",
});

const editForm = useForm({
    id: null,
    nama: "",
    nik: "",
    umur: 0,
    alamat: "",
    provinsi_id: "",
    kabupaten_id: "",
});

const filtered = computed(() => {
    return penduduks.value.filter((p) => {
        const matchesSearch =
            !search.value ||
            p.nama.toLowerCase().includes(search.value.toLowerCase()) ||
            p.nik.includes(search.value);

        const matchesProvinsi =
            !selectedProvinsi.value || p.provinsi_id == selectedProvinsi.value;

        const matchesKabupaten =
            !selectedKabupaten.value ||
            p.kabupaten_id == selectedKabupaten.value;

        return matchesSearch && matchesProvinsi && matchesKabupaten;
    });
});

const filteredKabupatens = computed(() => {
    const provinsiId = addForm.provinsi_id || editForm.provinsi_id;
    if (!provinsiId) return [];
    return kabupatens.value.filter((kab) => kab.provinsi_id == provinsiId);
});

const openEditDialog = (penduduk) => {
    editingId.value = penduduk.id;
    editForm.id = penduduk.id;
    editForm.nama = penduduk.nama;
    editForm.nik = penduduk.nik;
    editForm.umur = penduduk.umur;
    editForm.alamat = penduduk.alamat;
    editForm.provinsi_id = penduduk.provinsi_id;
    editForm.kabupaten_id = penduduk.kabupaten_id;
    showEditDialog.value = true;
};

const submitAddForm = () => {
    addForm.post(route("penduduk.store"), {
        preserveScroll: true,
        onSuccess: (res) => {
            // Optimistically add the new record
            penduduks.value.unshift({
                ...addForm.data(),
                id: res.props.last_id,
                provinsi: provinsis.value.find(
                    (p) => p.id === addForm.provinsi_id
                ),
                kabupaten: kabupatens.value.find(
                    (k) => k.id === addForm.kabupaten_id
                ),
            });
            addForm.reset();
            showAddDialog.value = false;
        },
        onError: () => {
            console.log("Error occurred:", addForm.errors);
        },
    });
};

const submitEditForm = () => {
    if (!editForm.id) {
        console.error("No ID available for update");
        return;
    }

    editForm.put(route("penduduk.update", editingId.value), {
        preserveScroll: true,
        onSuccess: () => {
            // Update the local record
            const index = penduduks.value.findIndex(
                (p) => p.id === editingId.value
            );
            if (index !== -1) {
                penduduks.value[index] = {
                    ...penduduks.value[index],
                    ...editForm.data(),
                    provinsi: provinsis.value.find(
                        (p) => p.id === editForm.provinsi_id
                    ),
                    kabupaten: kabupatens.value.find(
                        (k) => k.id === editForm.kabupaten_id
                    ),
                };
            }
            editForm.reset();
            showEditDialog.value = false;
        },
        onError: (errors) => {
            console.error("Update error:", errors);
        },
    });
};

const deletePenduduk = (id) => {
    if (confirm("Apakah Anda yakin ingin menghapus?")) {
        const index = penduduks.value.findIndex((p) => p.id === id);
        const [deletedPenduduk] = penduduks.value.splice(index, 1);

        router.delete(route("penduduk.destroy", id), {
            preserveScroll: true,
            onError: () => {
                // Rollback if error occurs
                penduduks.value.splice(index, 0, deletedPenduduk);
            },
        });
    }
};
</script>

<template>
    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
        <button
            class="bg-green-500 text-white px-4 py-2 rounded"
            @click="showAddDialog = true"
        >
            + Tambah
        </button>

        <!-- Add Dialog -->
        <div
            v-if="showAddDialog"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 rounded-lg w-full max-w-md space-y-4">
                <h2 class="text-xl font-bold mb-2">Tambah Penduduk</h2>

                <div>
                    <label class="block mb-2 text-sm font-medium">Nama</label>
                    <input
                        v-model="addForm.nama"
                        class="w-full border rounded px-3 py-2"
                    />
                    <p v-if="addForm.errors.nama" class="text-red-500 text-sm">
                        {{ addForm.errors.nama }}
                    </p>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">NIK</label>
                    <input
                        v-model="addForm.nik"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">Umur</label>
                    <input
                        type="number"
                        v-model="addForm.umur"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">Alamat</label>
                    <input
                        v-model="addForm.alamat"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium"
                        >Provinsi</label
                    >
                    <select
                        v-model="addForm.provinsi_id"
                        class="w-full border rounded px-3 py-2"
                    >
                        <option value="">Pilih Provinsi</option>
                        <option
                            v-for="prov in provinsis"
                            :key="prov.id"
                            :value="prov.id"
                        >
                            {{ prov.nama }}
                        </option>
                    </select>
                    <p
                        v-if="addForm.errors.provinsi_id"
                        class="text-red-500 text-sm"
                    >
                        {{ addForm.errors.provinsi_id }}
                    </p>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium"
                        >Kabupaten/Kota</label
                    >
                    <select
                        v-model="addForm.kabupaten_id"
                        class="w-full border rounded px-3 py-2"
                    >
                        <option value="">Pilih Kabupaten</option>
                        <option
                            v-for="kab in filteredKabupatens"
                            :key="kab.id"
                            :value="kab.id"
                        >
                            {{ kab.nama }}
                        </option>
                    </select>
                    <p
                        v-if="addForm.errors.kabupaten_id"
                        class="text-red-500 text-sm"
                    >
                        {{ addForm.errors.kabupaten_id }}
                    </p>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button
                        @click="showAddDialog = false"
                        class="bg-gray-300 px-4 py-2 rounded"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitAddForm"
                        class="bg-blue-600 text-white px-4 py-2 rounded"
                        :disabled="addForm.processing"
                    >
                        <span v-if="addForm.processing">Menyimpan...</span>
                        <span v-else>Simpan</span>
                    </button>
                </div>
            </div>
        </div>

        <div
            v-if="showEditDialog"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 rounded-lg w-full max-w-md space-y-4">
                <h2 class="text-xl font-bold mb-2">Edit Penduduk</h2>

                <div>
                    <label class="block mb-2 text-sm font-medium">Nama</label>
                    <input
                        v-model="editForm.nama"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">NIK</label>
                    <input
                        v-model="editForm.nik"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">Umur</label>
                    <input
                        type="number"
                        v-model="editForm.umur"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium">Alamat</label>
                    <input
                        v-model="editForm.alamat"
                        class="w-full border rounded px-3 py-2"
                    />
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium"
                        >Provinsi</label
                    >
                    <select
                        v-model="editForm.provinsi_id"
                        class="w-full border rounded px-3 py-2"
                    >
                        <option value="">Pilih Provinsi</option>
                        <option
                            v-for="prov in provinsis"
                            :key="prov.id"
                            :value="prov.id"
                        >
                            {{ prov.nama }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium"
                        >Kabupaten/Kota</label
                    >
                    <select
                        v-model="editForm.kabupaten_id"
                        class="w-full border rounded px-3 py-2"
                    >
                        <option value="">Pilih Kabupaten</option>
                        <option
                            v-for="kab in filteredKabupatens"
                            :key="kab.id"
                            :value="kab.id"
                        >
                            {{ kab.nama }}
                        </option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button
                        @click="showEditDialog = false"
                        class="bg-gray-300 px-4 py-2 rounded"
                    >
                        Batal
                    </button>
                    <button
                        @click="submitEditForm"
                        class="bg-blue-600 text-white px-4 py-2 rounded"
                        :disabled="editForm.processing"
                    >
                        <span v-if="editForm.processing">Mengupdate...</span>
                        <span v-else>Update</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="flex gap-2 flex-wrap">
            <select v-model="selectedProvinsi" class="border py-1 rounded">
                <option value="">Semua Provinsi</option>
                <option
                    v-for="prov in provinsis"
                    :key="prov.id"
                    :value="prov.id"
                >
                    {{ prov.nama }}
                </option>
            </select>

            <select v-model="selectedKabupaten" class="border py-1 rounded">
                <option value="">Semua Kabupaten</option>
                <option v-for="kab in kabupatens" :key="kab.id" :value="kab.id">
                    {{ kab.nama }}
                </option>
            </select>

            <input
                v-model="search"
                class="border px-3 py-1 rounded"
                placeholder="Cari Nama/NIK..."
            />
        </div>
    </div>

    <table class="w-full border text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">NIK</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Umur</th>
                <th class="px-4 py-2">Alamat</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="p in filtered" :key="p.id">
                <td class="border px-4 py-2">{{ p.nik }}</td>
                <td class="border px-4 py-2">{{ p.nama }}</td>
                <td class="border px-4 py-2">{{ p.umur }}</td>
                <td class="border px-4 py-2">{{ p.alamat }}</td>
                <td class="border px-4 py-2">
                    <button
                        class="text-blue-500 mr-2"
                        @click="openEditDialog(p)"
                    >
                        Edit
                    </button>
                    <button class="text-red-500" @click="deletePenduduk(p.id)">
                        Hapus
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</template>
