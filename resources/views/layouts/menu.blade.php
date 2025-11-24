{{-- ============================================================
     MENU GOVBR – FISCALIZER
   ============================================================ --}}

{{-- Todos os itens ficam dentro do <aside> --}}
<nav class="mt-3">

    <ul class="nav nav-pills nav-sidebar flex-column" role="menu" data-accordion="false" data-widget="treeview">

    {{-- =================== --}}
    {{-- PAINEL --}}
    {{-- =================== --}}
    <li class="sidebar-separator">Painel</li>

    <li class="nav-item">
        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="nav-icon fas fa-home"></i>
            <span class="nav-text">Painel</span>
        </a>
    </li>

    {{-- =================== --}}
    {{-- CONTRATOS --}}
    {{-- =================== --}}
    <li class="sidebar-separator">Contratos</li>

    <li class="nav-item has-treeview {{ request()->is('contratos*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->is('contratos*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-contract"></i>
            <span class="nav-text">Contratos</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('contratos.index') }}"
                   class="nav-link {{ request()->routeIs('contratos.index') ? 'active' : '' }}">
                   <span class="nav-text">Listar Contratos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('contratos.create') }}"
                   class="nav-link {{ request()->routeIs('contratos.create') ? 'active' : '' }}">
                   <span class="nav-text">Novo Contrato</span>
                </a>
            </li>
        </ul>
    </li>

    {{-- =================== --}}
    {{-- MEDIÇÃO --}}
    {{-- =================== --}}
    <li class="sidebar-separator">Medição</li>

    <li class="nav-item has-treeview {{ (request()->is('medicoes*') || request()->is('boletins*')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ (request()->is('medicoes*') || request()->is('boletins*')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-calculator"></i>
            <span class="nav-text">Medição</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('medicoes.index') }}"
                   class="nav-link {{ request()->routeIs('medicoes.index') ? 'active' : '' }}">
                   <span class="nav-text">Medições</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('boletins.index') }}"
                   class="nav-link {{ request()->routeIs('boletins.index') ? 'active' : '' }}">
                   <span class="nav-text">Boletins de Medição</span>
                </a>
            </li>
        </ul>
    </li>

    {{-- =================== --}}
    {{-- MONITORAMENTOS --}}
    {{-- =================== --}}
    <li class="sidebar-separator">Monitoramento</li>

    <li class="nav-item has-treeview {{ (request()->is('monitoramentos*') || request()->is('hosts*') || request()->is('host_testes*')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ (request()->is('monitoramentos*') || request()->is('hosts*') || request()->is('host_testes*')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-signal"></i>
            <span class="nav-text">Monitoramento</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('monitoramentos.index') }}"
                   class="nav-link {{ request()->routeIs('monitoramentos.index') ? 'active' : '' }}">
                   <span class="nav-text">Infraestrutura</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('monitoramentos.dashboard2') }}"
                   class="nav-link {{ request()->routeIs('monitoramentos.dashboard2') ? 'active' : '' }}">
                   <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('monitoramentos.heatline') }}"
                   class="nav-link {{ request()->routeIs('monitoramentos.heatline') ? 'active' : '' }}">
                   <span class="nav-text">Heatline</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('monitoramentos.matrix') }}"
                   class="nav-link {{ request()->routeIs('monitoramentos.matrix') ? 'active' : '' }}">
                   <span class="nav-text">Matriz</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('hosts.index') }}"
                   class="nav-link {{ request()->routeIs('hosts.index') ? 'active' : '' }}">
                   <span class="nav-text">Hosts</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('host_testes.dashboard') }}"
                   class="nav-link {{ request()->routeIs('host_testes.dashboard') ? 'active' : '' }}">
                   <span class="nav-text">Dashboard Conexões</span>
                </a>
            </li>
        </ul>
    </li>

    {{-- =================== --}}
    {{-- CADASTROS --}}
    {{-- =================== --}}
    <li class="sidebar-separator">Cadastros</li>

    <li class="nav-item has-treeview {{ (request()->is('empresas*') || request()->is('escolas*') || request()->is('dres*')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ (request()->is('empresas*') || request()->is('escolas*') || request()->is('dres*')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-address-book"></i>
            <span class="nav-text">Cadastros</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('empresas.index') }}"
                   class="nav-link {{ request()->routeIs('empresas.index') ? 'active' : '' }}">
                   <span class="nav-text">Empresas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('escolas.index') }}"
                   class="nav-link {{ request()->routeIs('escolas.index') ? 'active' : '' }}">
                   <span class="nav-text">Escolas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('dres.index') }}"
                   class="nav-link {{ request()->routeIs('dres.index') ? 'active' : '' }}">
                   <span class="nav-text">DREs</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="sidebar-separator">Contratações</li>

    <li class="nav-item has-treeview {{ request()->is('contratacoes*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->is('contratacoes*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-clipboard-list"></i>
            <span class="nav-text">Contratações</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('contratacoes.index') }}"
                   class="nav-link {{ request()->routeIs('contratacoes.index') ? 'active' : '' }}">
                   <span class="nav-text">Painel</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('contratacoes.termos-referencia.index') }}"
                   class="nav-link {{ request()->routeIs('contratacoes.termos-referencia.index') ? 'active' : '' }}">
                   <span class="nav-text">Termos de Referência</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('contratacoes.termos-referencia.create') }}"
                   class="nav-link {{ request()->routeIs('contratacoes.termos-referencia.create') ? 'active' : '' }}">
                   <span class="nav-text">Novo TR</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="sidebar-separator">Projetos</li>

    <li class="nav-item has-treeview {{ (request()->is('projetos*') || request()->is('dashboard/projetos')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ (request()->is('projetos*') || request()->is('dashboard/projetos')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-diagram-project"></i>
            <span class="nav-text">Projetos</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('dashboard.projetos') }}"
                   class="nav-link {{ request()->routeIs('dashboard.projetos') ? 'active' : '' }}">
                   <span class="nav-text">Painel de Projetos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('projetos.index') }}"
                   class="nav-link {{ request()->routeIs('projetos.index') ? 'active' : '' }}">
                   <span class="nav-text">Listar Projetos</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('projetos.create') }}"
                   class="nav-link {{ request()->routeIs('projetos.create') ? 'active' : '' }}">
                   <span class="nav-text">Novo Projeto</span>
                </a>
            </li>
        </ul>
    </li>

    {{-- =================== --}}
    {{-- ADMINISTRATIVO --}}
    {{-- =================== --}}
    <li class="sidebar-separator">Relatórios</li>

    <li class="nav-item has-treeview {{ request()->is('relatorios*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ request()->is('relatorios*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-alt"></i>
            <span class="nav-text">Relatórios</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('relatorios.index') }}"
                   class="nav-link {{ request()->routeIs('relatorios.index') ? 'active' : '' }}">
                   <span class="nav-text">Listar Relatórios</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('relatorios.gerar') }}"
                   class="nav-link {{ request()->routeIs('relatorios.gerar') ? 'active' : '' }}">
                   <span class="nav-text">Gerar Relatório</span>
                </a>
            </li>
        </ul>
    </li>

    <li class="sidebar-separator">Administração</li>

    <li class="nav-item has-treeview {{ (request()->is('usuarios*') || request()->is('situacoes*') || request()->is('rbac*')) ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ (request()->is('usuarios*') || request()->is('situacoes*') || request()->is('rbac*')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-users-cog"></i>
            <span class="nav-text">Administração</span>
            <i class="right fas fa-angle-left"></i>
        </a>

        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}"
                   class="nav-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}">
                   <span class="nav-text">Usuários</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('situacoes.index') }}"
                   class="nav-link {{ request()->routeIs('situacoes.index') ? 'active' : '' }}">
                   <span class="nav-text">Situações de Contrato</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('rbac.roles_actions.index') }}"
                   class="nav-link {{ request()->routeIs('rbac.roles_actions.index') ? 'active' : '' }}">
                   <span class="nav-text">Permissões</span>
                </a>
            </li>
        </ul>
    </li>

    </ul>
</nav>
