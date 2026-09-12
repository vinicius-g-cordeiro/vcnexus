export default {
  header: {
    dropdown: {
      profile: "Perfil",
      settings: "Configurações",
      logout: "Deslogar",
      login: "Login",
      register: "Registrar",
      search: "Sair",
    },
    searchbar: 'Pesquisar',
    links: {
        home: 'Inicio',
        about: 'Sobre',
        schedule: {
            schedule: 'Agenda',
            calendar: 'Calendário',
            list: 'Listar',
            new: 'Novo'
        },
        tasks: {
            tasks: 'Tarefas',
            new: 'Novo',
            list: 'Listar'
        },
        users: {
            users: 'Usuários',
            new: 'Novo',
            list: 'Listar',
            documents: 'Documentos',
            reports: 'Relatórios',
        },
        tenants: {
            tenants: 'Organização',
            new: 'Novo',
            list: 'Listar',
            reports: 'Relatórios',
        }
    }
  },
  errors: {
    404: {
      code: "ERRO 404",
      titlePrefix: "Página ",
      titleHighlight: "não",
      titleSuffix: " encontrada",
      description:
        "A página que você está procurando não existe ou pode ter sido movida.",
      back: "Voltar ao painel",
    },
  },
  users: {
    list: {
      search: {
        legend: "Usuários - Pesquisar",
        search: 'Pesquisar',
        order: {
          label: 'Ordem',
        },
        active: {
          label: 'Ativo',
          active: 'Ativo',
          deactivated: 'Desativado' 
        },
        blocked: {
          label: 'Bloqueado',
          blocked: 'Bloqueado',
          unblocked: 'Desbloqueado' 
        },
        order_by: 'Ordenar por',
        searchbtn: 'Pesquisar',
        clear: 'Limpar'
      },
      actions: {
        new: 'Novo',
        reports: 'Relatórios',
        documents: 'Documentos'
      },
      results: {
        legend: 'Lista de usuários',
        loading: 'Carregando...',
        errors: 'Ocorreu um erro...',
        empty: 'Nenhum resultado encontrado..',
        deactivated: 'Desativado',
        active: 'Ativado',
        blocked: 'Bloqueado',
        headers : {
          userInfo: 'Usuário',
          status: 'Status',
          organization: 'Organização',
          actions: 'Ações',
          created_at: 'Criado em',
          updated_at: 'Atualizado em',
          deleted_at: 'Desativado em',
          deleted_by: 'Desativado por',
          blocked_at: 'Bloqueado em',
          last_login: 'Último login',
        },
        actions: {
          view: 'Visualizar',
          edit: 'Editar',
          delete: 'Desativar',
          activate: 'Ativar',
          block: 'Bloquear',
          unblock: 'Desbloquear',
        }
      }
    },
  },
  tenants: {
    list: {
      search: {
        legend: "Organizações - Pesquisar",
        search: 'Pesquisa',
        order: {
          label: 'Ordernar',
        },
        active: {
          label: 'Ativo',
          active: 'Ativo',
          deactivated: 'Desativado' 
        },
        order_by: 'Ordernar por',
        searchbtn: 'Pesquisar',
        clear: 'Limpar'
      },
      actions: {
        new: 'Novo',
        reports: 'Relatorio',
        documents: 'Documentos',
        users: 'Usuários'
      },
      results: {
        legend: 'Lista de Organizações',
        loading: 'Carregando...',
        errors: 'Ocorreu um erro.',
        active: 'Ativo',
        deactivated: 'Desativado',
        blocked: 'Bloqueado',
        headers : {
          tenantInfo: 'Organização',
          organization: 'Organização',
          status: 'Status',
          actions: 'Ações',
          created_at: 'Criado em',
          updated_at: 'Atualizado em',
          blocked_at: 'Bloqueado em',
        },
        actions: {
          view: 'Visualizar',
          edit: 'Editar',
          delete: 'Desativar',
          activate: 'Ativar',
        }
      }
    },
  }
};