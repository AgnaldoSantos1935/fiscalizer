# core/speedtest_utils.py
import speedtest

def testar_velocidade() -> dict:
    s = speedtest.Speedtest()
    s.get_best_server()
    down = s.download() / 1e6  # Mbps
    up = s.upload() / 1e6      # Mbps
    return {
        "download_mbps": round(down, 2),
        "upload_mbps": round(up, 2),
        "latencia_ms": s.results.ping,
        "server": s.results.server.get("name")
    }
