# config.py
from pydantic import BaseSettings

class Settings(BaseSettings):
    api_key: str = "SUA_CHAVE_SECRETA"  # opcional
    class Config:
        env_file = ".env"

settings = Settings()
