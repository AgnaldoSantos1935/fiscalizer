# models.py
from sqlalchemy import Column, Integer, String, Float, Boolean, DateTime
from datetime import datetime
from db import Base

class HostStatus(Base):
    __tablename__ = "host_status"

    id = Column(Integer, primary_key=True, index=True)
    agent_id = Column(String, index=True)      # escola, ponto remoto, etc.
    host_label = Column(String, index=True)    # nome amigável
    ip = Column(String, index=True)

    alive = Column(Boolean, default=False)
    latencia_ms = Column(Float, nullable=True)
    metodo = Column(String, default="ICMP")    # ICMP ou TCP

    created_at = Column(DateTime, default=datetime.utcnow)
