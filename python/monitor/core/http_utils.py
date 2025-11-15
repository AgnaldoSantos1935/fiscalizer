# core/http_utils.py
import httpx

async def http_test(url: str, timeout: float = 2.0) -> dict:
    try:
        async with httpx.AsyncClient(timeout=timeout) as client:
            resp = await client.get(url)
        return {
            "ok": resp.status_code < 400,
            "status_code": resp.status_code,
            "latencia_ms": resp.elapsed.total_seconds() * 1000,
        }
    except Exception:
        return {"ok": False, "status_code": None, "latencia_ms": None}
