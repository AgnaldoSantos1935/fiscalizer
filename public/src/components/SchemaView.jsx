
export default function SchemaView({ schema, loading }) {
  if (loading) return <p className="p-4">Carregando schema...</p>;
  if (!schema) return <p className="p-4">Nenhuma tabela encontrada.</p>;

  return (
    <div className="p-4">
      <h2 className="text-lg font-bold mb-4">Tabelas</h2>
      {Object.keys(schema).map(t => (
        <details key={t} className="mb-4">
          <summary className="cursor-pointer">{t}</summary>
          <pre className="bg-black/20 p-2 mt-2 rounded">{schema[t].ddl}</pre>
        </details>
      ))}
    </div>
  );
}
