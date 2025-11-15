# core/ping_utils.py
import aioping
import asyncio
import time

async def icmp_ping(host: str, timeout: float = 1.0) -> dict:
    try:
        delay = await aioping.ping(host, timeout=timeout)  # ICMP
        return {"alive": True, "latencia_ms": delay * 1000, "metodo": "ICMP"}
    except Exception:
        return {"alive": False, "latencia_ms": None, "metodo": "ICMP"}

async def tcp_ping(host: str, port: int = 80, timeout: float = 1.0) -> dict:
    inicio = time.perf_counter()
    try:
        reader, writer = await asyncio.wait_for(
            asyncio.open_connection(host, port),
            timeout=timeout
        )
        writer.close()
        await writer.wait_closed()
        return {
            "alive": True,
            "latencia_ms": (time.perf_counter() - inicio) * 1000,
            "metodo": "TCP"
        }
    except Exception:
        return {"alive": False, "latencia_ms": None, "metodo": "TCP"}

async def smart_ping(host: str) -> dict:
    # 1) tenta ICMP
    res_icmp = await icmp_ping(host)
    if res_icmp["alive"]:
        return res_icmp

    # 2) fallback TCP
    res_tcp = await tcp_ping(host)
    return res_tcp
