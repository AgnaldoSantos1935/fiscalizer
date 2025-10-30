# pip install pandas sqlalchemy pymysql geopandas shapely matplotlib
import pandas as pd
from sqlalchemy import create_engine
import geopandas as gpd
from shapely.geometry import Point
import matplotlib.pyplot as plt

# 1) Ler do MySQL
engine = create_engine("mysql+pymysql://USUARIO:SENHA@HOST:PORTA/NOME_DA_BASE?charset=utf8mb4")
df = pd.read_sql("""
    SELECT codigo, Escola, latitude AS lat, longitude AS lon, Municipio, dre
    FROM escolas
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
""", engine)

# 2) Converter para GeoDataFrame (assumindo WGS84)
gdf = gpd.GeoDataFrame(
    df,
    geometry=[Point(xy) for xy in zip(df['lon'], df['lat'])],
    crs="EPSG:4326"
)

# (Opcional) 3) Carregar o contorno do Pará para recorte/plot
# Substitua pelo caminho do seu arquivo .shp/.geojson do Pará
para = gpd.read_file("dados/para_limite.geojson").to_crs("EPSG:4326")

# 4) Plot simples
fig, ax = plt.subplots(figsize=(10, 10))
para.boundary.plot(ax=ax, linewidth=1)
gdf.plot(ax=ax, markersize=5)
ax.set_title("Escolas no Estado do Pará (EPSG:4326)")
ax.set_axis_off()
plt.tight_layout()
plt.savefig("mapa_escolas_para.png", dpi=300)

# (Opcional) exportar GeoJSON para usar na web
gdf[['codigo','Escola','Municipio','dre','geometry']].to_file("escolas_para.geojson", driver="GeoJSON")
