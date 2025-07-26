<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const { kabupatens, provinsis, penduduks } = usePage().props;
const provinsiSearch = ref("");
const kabupatenSearch = ref("");
const selectedProvinsi = ref("");

// Calculate province populations from kabupaten data
const provinsiData = computed(() => {
    return provinsis.map((prov) => {
        const totalPenduduk = kabupatens
            .filter((kab) => kab.provinsi_id === prov.id)
            .reduce((sum, kab) => sum + (kab.jumlah_penduduk || 0), 0);

        return {
            ...prov,
            jumlah_penduduk: totalPenduduk,
        };
    });
});

// Prepare kabupaten data with province names
const kabupatenData = computed(() => {
    return kabupatens.map((kab) => ({
        ...kab,
        provinsi: provinsis.find((p) => p.id === kab.provinsi_id),
    }));
});

// Filtered data
const filteredProvinsi = computed(() => {
    return provinsiData.value.filter((prov) =>
        prov.nama.toLowerCase().includes(provinsiSearch.value.toLowerCase())
    );
});

const filteredKabupaten = computed(() => {
    return kabupatenData.value.filter((kab) => {
        const matchesSearch = kab.nama
            .toLowerCase()
            .includes(kabupatenSearch.value.toLowerCase());
        const matchesProvinsi =
            !selectedProvinsi.value ||
            kab.provinsi_id == selectedProvinsi.value;
        return matchesSearch && matchesProvinsi;
    });
});

// Format number helper
const formatNumber = (num) => {
    return new Intl.NumberFormat("id-ID").format(num || 0);
};
console.log(filteredKabupaten);

// Export to HTML functions
const exportProvinsiHTML = () => {
    const html = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Laporan Jumlah Penduduk per Provinsi</title>
      <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
      </style>
    </head>
    <body>
      <h1>Laporan Jumlah Penduduk per Provinsi</h1>
      <table>
        <thead>
          <tr>
            <th>Provinsi</th>
            <th>Jumlah Penduduk</th>
          </tr>
        </thead>
        <tbody>
          ${filteredProvinsi.value
              .map(
                  (prov) => `
            <tr>
              <td>${prov.nama}</td>
              <td>${formatNumber(prov.jumlah_penduduk)}</td>
            </tr>
          `
              )
              .join("")}
        </tbody>
      </table>
      <p>Total: ${formatNumber(
          filteredProvinsi.value.reduce(
              (sum, prov) => sum + prov.jumlah_penduduk,
              0
          )
      )}</p>
      <p>Tanggal cetak: ${new Date().toLocaleDateString("id-ID")}</p>
    </body>
    </html>
  `;

    printHTML(html);
};

const exportKabupatenHTML = () => {
    const filtered = filteredKabupaten.value;
    const selectedProv = selectedProvinsi.value
        ? provinsis.find((p) => p.id == selectedProvinsi.value)?.nama
        : "Semua Provinsi";

    const html = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Laporan Jumlah Penduduk per Kabupaten</title>
      <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        .filter-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
      </style>
    </head>
    <body>
      <h1>Laporan Jumlah Penduduk per Kabupaten</h1>
      <div class="filter-info">
        <p><strong>Provinsi:</strong> ${selectedProv}</p>
      </div>
      <table>
        <thead>
          <tr>
            <th>Kabupaten/Kota</th>
            <th>Provinsi</th>
            <th>Jumlah Penduduk</th>
          </tr>
        </thead>
        <tbody>
          ${filtered
              .map(
                  (kab) => `
            <tr>
              <td>${kab.nama}</td>
              <td>${kab.provinsi?.nama || "-"}</td>
              <td>${formatNumber(kab.jumlah_penduduk)}</td>
            </tr>
          `
              )
              .join("")}
        </tbody>
      </table>
      <p>Total: ${formatNumber(
          filtered.reduce((sum, kab) => sum + kab.jumlah_penduduk, 0)
      )}</p>
      <p>Tanggal cetak: ${new Date().toLocaleDateString("id-ID")}</p>
    </body>
    </html>
  `;

    printHTML(html);
};

const printHTML = (html) => {
    const printWindow = window.open("", "_blank");
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
};
</script>
<template>
    <div class="">
        <!-- Provinsi Report Section -->
        <section class="mb-12">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Laporan Provinsi</h2>
                <div class="flex gap-2">
                    <button
                        @click="exportProvinsiHTML"
                        class="px-4 py-2 bg-blue-500 text-white rounded"
                    >
                        Export HTML
                    </button>
                    <input
                        type="text"
                        v-model="provinsiSearch"
                        placeholder="Search..."
                        class="border px-3 py-1 rounded"
                    />
                </div>
            </div>

            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Provinsi</th>
                        <th class="px-4 py-2 text-left">Jumlah Penduduk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="prov in filteredProvinsi" :key="prov.id">
                        <td class="border px-4 py-2">{{ prov.nama }}</td>
                        <td class="border px-4 py-2">
                            {{ formatNumber(prov.jumlah_penduduk) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Kabupaten Report Section -->
        <section>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Laporan Kabupaten/Kota</h2>
                <div class="flex gap-2">
                    <button
                        @click="exportKabupatenHTML"
                        class="px-4 py-2 bg-blue-500 text-white rounded"
                    >
                        Export HTML
                    </button>
                    <select
                        v-model="selectedProvinsi"
                        class="border  py-1 rounded"
                    >
                        <option value="">Semua Provinsi</option>
                        <option
                            v-for="prov in provinsis"
                            :key="prov.id"
                            :value="prov.id"
                        >
                            {{ prov.nama }}
                        </option>
                    </select>
                    <input
                        type="text"
                        v-model="kabupatenSearch"
                        placeholder="Search..."
                        class="border px-3 py-1 rounded"
                    />
                </div>
            </div>

            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Kabupaten/Kota</th>
                        <th class="px-4 py-2 text-left">Provinsi</th>
                        <th class="px-4 py-2 text-left">Jumlah Penduduk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="kab in filteredKabupaten" :key="kab.id">
                        <td class="border px-4 py-2">{{ kab.nama }}</td>
                        <td class="border px-4 py-2">
                            {{ kab.provinsi?.nama }}
                        </td>
                        <td class="border px-4 py-2">
                            {{ kab.jumlah_penduduk }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Hidden print container -->
        <div ref="printContainer" class="hidden"></div>
    </div>
</template>
<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
}

th,
td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
}

.hidden {
    display: none;
}
</style>
