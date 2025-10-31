# Instalar dependências se necessário:
# pip install pandas sqlalchemy pymysql geopandas shapely folium branca matplotlib

import pandas as pd
from sqlalchemy import create_engine
import geopandas as gpd
from shapely.geometry import Point
import folium
from folium.plugins import MarkerCluster
import branca
import matplotlib.pyplot as plt

# ============================
# 1) CONEXÃO COM O BANCO
# ============================
USER = "appuser"
PASSWORD = "S@n#t0s.123"
HOST = "191.252.109.76"
PORT = 3306
DB = "db_fiscalizer"

engine = create_engine(
    f"mysql+pymysql://{USER}:{PASSWORD}@{HOST}:{PORT}/{DB}?charset=utf8mb4"
)

# ============================
# 2) CONSULTA AO BANCO
# ============================
query = """
    SELECT codigo, Escola, latitude AS lat, longitude AS lon, Municipio, dre
    FROM escolas
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
"""
df = pd.read_sql(query, engine)

# ============================
# 3) GEOLOCALIZAÇÃO
# ============================
df['lat'] = pd.to_numeric(df['lat'], errors='coerce')
df['lon'] = pd.to_numeric(df['lon'], errors='coerce')
df = df.dropna(subset=['lat', 'lon'])

gdf = gpd.GeoDataFrame(
    df,
    geometry=[Point(xy) for xy in zip(df['lon'], df['lat'])],
    crs="EPSG:4326"
)

# ============================
# 4) LIMITES GEOGRÁFICOS
# ============================
para = gpd.read_file("dados/para_limite.geojson").to_crs("EPSG:4326")
muni = gpd.read_file("dados/para_municipios.geojson").to_crs("EPSG:4326")

# Interseção: filtra apenas escolas dentro do Pará
gdf_para = gpd.clip(gdf, para)

# ============================
# 5) CONTAGEM POR MUNICÍPIO E DRE
# ============================
join = gpd.sjoin(gdf_para, muni, how="inner", predicate="within")
counts_mun = join.groupby("NM_MUNICIP").size().reset_index(name="qtd_escolas")
counts_dre = gdf_para.groupby("dre").size().reset_index(name="qtd_escolas")

# Une ao shapefile
muni = muni.merge(counts_mun, on="NM_MUNICIP", how="left").fillna(0)

# Exporta CSV de relatório
relatorio_csv = "relatorio_escolas_por_municipio.csv"
counts_mun.to_csv(relatorio_csv, index=False, encoding="utf-8")
print(f"📊 Relatório CSV gerado: {relatorio_csv}")

# ============================
# 6) MAPA BASE
# ============================
m = folium.Map(location=[-3.5, -52.0], zoom_start=5.5, tiles="CartoDB positron")

# Contorno do estado
folium.GeoJson(
    para,
    name="Limite do Pará",
    style_function=lambda x: {"color": "black", "weight": 1, "fillOpacity": 0},
).add_to(m)

# ============================
# 7) CAMADA DE CALOR (CHOROPLETH)
# ============================
choropleth = folium.Choropleth(
    geo_data=muni,
    name="Densidade de Escolas por Município",
    data=muni,
    columns=["NM_MUNICIP", "qtd_escolas"],
    key_on="feature.properties.NM_MUNICIP",
    fill_color="YlOrRd",
    fill_opacity=0.7,
    line_opacity=0.2,
    legend_name="Número de Escolas",
).add_to(m)

# ============================
# 8) CLUSTERS INTERATIVOS
# ============================
clusters = MarkerCluster(name="Escolas Individuais").add_to(m)
for _, row in gdf_para.iterrows():
    folium.CircleMarker(
        location=[row["lat"], row["lon"]],
        radius=3,
        color="blue",
        fill=True,
        fill_opacity=0.6,
        popup=folium.Popup(
            f"<b>{row['Escola']}</b><br>"
            f"Código: {row['codigo']}<br>"
            f"Município: {row['Municipio']}<br>"
            f"DRE: {row['dre']}",
            max_width=300,
        ),
    ).add_to(clusters)

# ============================
# 9) PAINEL LATERAL (BRANCA)
# ============================
total_escolas = len(gdf_para)
total_mun = muni.shape[0]
total_dre = counts_dre.shape[0]

html_panel = f"""
<div style="
    position: fixed;
    top: 20px; right: 20px;
    width: 300px; height: auto;
    background-color: white;
    border: 2px solid gray; border-radius: 10px;
    padding: 15px; z-index:9999;
    font-family: Arial; font-size: 12px;
    box-shadow: 2px 2px 5px rgba(0,0,0,0.3);">
<h4 style="margin-top:0;">📍 Painel de Escolas do Pará</h4>
<b>Total de Escolas:</b> {total_escolas:,}<br>
<b>Total de Municípios:</b> {total_mun:,}<br>
<b>Total de DREs:</b> {total_dre:,}<br><br>
<b>Top 5 Municípios com mais escolas:</b><br>
"""

top5 = counts_mun.sort_values("qtd_escolas", ascending=False).head(5)
for _, r in top5.iterrows():
    html_panel += f"• {r['NM_MUNICIP']}: {int(r['qtd_escolas'])} escolas<br>"

html_panel += """
<hr>
<small>Fonte: DB_Fiscalizer / SEDUC-PA<br>
Atualizado automaticamente via Python</small>
</div>
"""
m.get_root().html.add_child(folium.Element(html_panel))

# ============================
# 10) CONTROLE E EXPORTAÇÃO
# ============================
folium.LayerControl(collapsed=False).add_to(m)
saida_html = "mapa_painel_escolas_para.html"
m.save(saida_html)

print(f"✅ Painel interativo gerado com sucesso: {saida_html}")
