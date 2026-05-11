export const columns = [
    { accessorKey: "data_proposta", header: "Data" },
    { accessorKey: "numero", header: "Número" },
    { accessorKey: "validade", header: "Validade" },
    { accessorKey: "cliente.nome", header: "Cliente" },
    {
        accessorKey: "total",
        header: "Valor Total",
        cell: ({ row }) => `€ ${row.original.total.toFixed(2)}`
    },
    { accessorKey: "estado", header: "Estado" },
    {
        id: "actions",
        header: "Ações",
        cell: ({ row }) => `
            <div class="flex space-x-2">
                <a href="/propostas/${row.original.id}/edit" class="text-blue-600">Editar</a>
                <button onclick="deleteProposta(${row.original.id})" class="text-red-600">Excluir</button>
            </div>
        `
    }
];
