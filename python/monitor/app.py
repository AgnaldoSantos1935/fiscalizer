# app.py
from fastapi import FastAPI, Depends, HTTPException
from pydantic import BaseModel
from typing import List, Optional
from core.ping_utils import smart_ping
from core.http_utils import http_test
from core.speedtest_utils import testar_velocidade
from db import get_db, Base, engine
from models import HostStatus

Base.metadata.create_all(bind=engine)

app = FastAPI(title="Monitor Backend Fiscalizer")

class PingTarget(BaseModel):
    id: str
    ip: str
    label: Optional[str] = None

class PingResult(BaseModel):
    id: str
    ip: str
    label: Optional[str]
    alive: bool
    latencia_ms: Optional[float]
    metodo: str

class AgentReport(BaseModel):
    agent_id: str
    ip: str
    host_label: str
    alive: bool
    latencia_ms: Optional[float]
    metodo: str

@app.get("/health")
def health():
    return {"status": "ok"}

@app.post("/tests/ping", response_model=List[PingResult])
async def run_ping(targets: List[PingTarget]):
    results = []
    import asyncio
    coros = [smart_ping(t.ip) for t in targets]
    raw = await asyncio.gather(*coros)
    for t, r in zip(targets, raw):
        results.append(PingResult(
            id=t.id,
            ip=t.ip,
            label=t.label,
            alive=r["alive"],
            latencia_ms=r["latencia_ms"],
            metodo=r["metodo"],
        ))
    return results

@app.get("/tests/http")
async def run_http(url: str):
    return await http_test(url)

@app.get("/tests/speedtest")
def run_speedtest():
    return testar_velocidade()

@app.post("/agent/report")
def agent_report(report: AgentReport):
    with get_db() as db:
        hs = HostStatus(
            agent_id=report.agent_id,
            host_label=report.host_label,
            ip=report.ip,
            alive=report.alive,
            latencia_ms=report.latencia_ms,
            metodo=report.metodo,
        )
        db.add(hs)
        db.commit()
    return {"status": "ok"}

class SlaRequest(BaseModel):
    agent_id: str
    host_label: Optional[str] = None
    # em produção você usaria intervalo de datas, aqui simplificado

@app.post("/telco/sla")
def telco_sla(req: SlaRequest):
    with get_db() as db:
        q = db.query(HostStatus).filter(HostStatus.agent_id == req.agent_id)
        if req.host_label:
            q = q.filter(HostStatus.host_label == req.host_label)
        registros = q.all()

    if not registros:
        raise HTTPException(status_code=404, detail="Sem dados para SLA")

    total = len(registros)
    vivos = len([r for r in registros if r.alive])

    uptime = (vivos / total) * 100
    return {
        "agent_id": req.agent_id,
        "host_label": req.host_label,
        "total_registros": total,
        "uptime_percent": round(uptime, 2)
    }
