<script setup>
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";

const { provinsis: propProvinsis } = defineProps({ provinsis: Array });
const provinsis = ref([...propProvinsis]);

watch(
    () => propProvinsis,
    (newVal) => {
        provinsis.value = [...newVal];
    },
    { deep: true }
);

const search = ref("");
const showAddDialog = ref(false);
const showEditDialog = ref(false);
const editingProvinsi = ref(null);

const form = useForm({
    id: null,
    nama: "",
});

const filtered = computed(() =>
    search.value
        ? provinsis.value.filter((p) =>
              p.nama.toLowerCase().includes(search.value.toLowerCase())
          )
        : provinsis.value
);

function handleSave() {
    if (form.nama.trim() === "") return;
    form.post("/provinsi", {
        preserveScroll: true,
        onSuccess: (res) => {
            provinsis.value.push({
                ...form.data(),
                id: res.props?.last_id,
            });
            form.reset();
            showAddDialog.value = false;
        },
    });
}

function editProvinsi(p) {
    editingProvinsi.value = { ...p };
    form.id = p.id;
    form.nama = p.nama;
    showEditDialog.value = true;
}

function updateProvinsi() {
    if (!form.id) {
        console.error("No ID available for update");
        return;
    }
    if (!editingProvinsi.value) return;

    form.put(`/provinsi/${editingProvinsi.value.id}`, {
        onSuccess: () => {
            showEditDialog.value = false;
            // Update lokal
            const index = provinsis.value.findIndex(
                (p) => p.id === editingProvinsi.value.id
            );
            if (index !== -1) {
                provinsis.value[index].nama = form.nama;
            }
            form.reset();
        },
    });
}

function deleteProvinsi(p) {
    form.delete(`/provinsi/${p.id}`, {
        onSuccess: () => {
            provinsis.value = provinsis.value.filter(
                (prov) => prov.id !== p.id
            );
            form.reset();
        },
    });
}
</script>

<template>
    <div class="flex justify-between items-center mb-4">
        <button
            class="bg-green-500 text-white px-4 py-2 rounded"
            @click="showAddDialog = true"
        >
            + Tambah
        </button>
        <input
            v-model="search"
            class="border px-3 py-1 rounded"
            placeholder="Cari Provinsi..."
        />
    </div>

    <!-- Tambah Dialog -->
    <div
        v-if="showAddDialog"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded shadow w-full max-w-sm">
            <h2 class="text-lg font-bold mb-4">Tambah Provinsi</h2>

            <label class="block mb-2 text-sm font-medium">Nama Provinsi</label>
            <input
                v-model="form.nama"
                class="w-full border px-3 py-2 rounded mb-1"
                placeholder="Masukkan nama provinsi"
            />
            <p v-if="form.errors.nama" class="text-red-500 text-sm">
                {{ form.errors.nama }}
            </p>

            <div class="flex justify-end gap-2 mt-4">
                <button
                    class="px-4 py-2 rounded border"
                    @click="showAddDialog = false"
                >
                    Batal
                </button>
                <button
                    :disabled="form.processing"
                    class="bg-blue-500 text-white px-4 py-2 rounded"
                    @click="handleSave"
                >
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Dialog -->
    <div
        v-if="showEditDialog"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded shadow w-full max-w-sm">
            <h2 class="text-lg font-bold mb-4">Edit Provinsi</h2>

            <label class="block mb-2 text-sm font-medium">Nama Provinsi</label>
            <input
                v-model="form.nama"
                class="w-full border px-3 py-2 rounded mb-1"
                placeholder="Masukkan nama provinsi"
            />
            <p v-if="form.errors.nama" class="text-red-500 text-sm">
                {{ form.errors.nama }}
            </p>

            <div class="flex justify-end gap-2 mt-4">
                <button
                    class="px-4 py-2 rounded border"
                    @click="showEditDialog = false"
                >
                    Batal
                </button>
                <button
                    :disabled="form.processing"
                    class="bg-blue-500 text-white px-4 py-2 rounded"
                    @click="updateProvinsi"
                >
                    Update
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="w-full border text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="prov in filtered" :key="prov.id">
                <td class="border px-4 py-2">{{ prov.nama }}</td>
                <td class="border px-4 py-2">
                    <button
                        class="text-blue-500 mr-2"
                        @click="editProvinsi(prov)"
                    >
                        Edit
                    </button>
                    <button class="text-red-500" @click="deleteProvinsi(prov)">
                        Hapus
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</template>
