
import { useState } from "react";

export default function AnalysisView() {
  const [loading, setLoading] = useState(false);
  const [analysis, setAnalysis] = useState("");

  const run = () => {
    setLoading(true);
    fetch("http://localhost:4000/api/analyze", { method: "POST" })
      .then(r => r.json())
      .then(d => { setAnalysis(d.analysis); setLoading(false); });
  };

  return (
    <div className="p-4">
      <button onClick={run} className="px-4 py-2 bg-emerald-500 text-black rounded">
        Rodar Análise IA
      </button>

      {loading && <p className="mt-4">Gerando análise...</p>}

      {analysis && (
        <pre className="whitespace-pre-wrap mt-4 bg-black/20 p-3 rounded">
          {analysis}
        </pre>
      )}
    </div>
  );
}
