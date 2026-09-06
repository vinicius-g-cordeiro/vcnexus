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
            list: 'Listar'
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
        headers : {
          userInfo: 'Usuário',
          status: 'Status',
          actions: 'Ações',
          created_at: 'Criado',
          updated_at: 'Atualizado',
          last_login: 'Último login',
        },
        actions: {
          view: 'Visualizar',
          edit: 'Editar',
          delete: 'Desativar',
          block: 'Bloquear',
        }
      }
    },
  },
  tenants: {
    list: {
      search: {
        legend: "Organização - Pesquisa",
        search: 'Pesquisa',
        order: {
          label: 'Ordenar',
        },
        active: {
          label: 'Ativo',
          active: 'Ativo',
          deactivated: 'Desativado' 
        },
        order_by: 'Ordenar por',
        searchbtn: 'Pesquisa',
        clear: 'Limpar'
      },
      actions: {
        new: 'Novo',
        reports: 'Relatórios',
        users: 'Usuários'
      },

      results: {
        legend: 'Lista de Organizações',
        loading: 'Carregando...',
        errors: 'Ocorreu um erro.',
        empty: 'Nenhum resultado encontrado..',
        headers : {
          tenantInfo: 'Organização',
          status: 'Status',
          actions: 'Ações',
          created_at: 'Criado',
          updated_at: 'Atualizado',
          last_login: 'Último login',
        },
        actions: {
          view: 'Visualizar',
          edit: 'Editar',
          delete: 'Desativar',
          block: 'Bloquear',
        }
      }
    },
  }
};