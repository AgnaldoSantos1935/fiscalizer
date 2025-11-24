
import { useEffect, useState } from "react";
import SchemaView from "./components/SchemaView";
import AnalysisView from "./components/AnalysisView";

const API = "http://localhost:4000";

export default function App() {
  const [schema, setSchema] = useState(null);
  const [loading, setLoading] = useState(true);
  const [tab, setTab] = useState("schema");

  useEffect(() => {
    fetch(API + "/api/schema")
      .then(r => r.json())
      .then(d => { setSchema(d.schema); setLoading(false); });
  }, []);

  return (
    <div className="flex min-h-screen">
      <aside className="w-64 bg-slate-900 p-4 space-y-2 border-r border-slate-800">
        <h1 className="text-xl font-bold">Fiscalizer IA</h1>
        <button onClick={() => setTab("schema")} className="block w-full text-left py-2 hover:bg-slate-800 rounded">📂 Estrutura</button>
        <button onClick={() => setTab("analysis")} className="block w-full text-left py-2 hover:bg-slate-800 rounded">🤖 Análise IA</button>
      </aside>

      <main className="flex-1">
        {tab === "schema" && <SchemaView schema={schema} loading={loading} />}
        {tab === "analysis" && <AnalysisView />}
      </main>
    </div>
  )
}
