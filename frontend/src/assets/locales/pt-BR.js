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
        entity: {
            entity: 'Entidade',
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
      results: {
        legend: 'Lista de usuários',
        name: 'Nome',
        created_at: 'Data de criação',
        updated_at: 'Data de atualização',
        email: 'Email',
        actions: 'Ações',
        organization: 'Organização',
        errors: 'Ocorreu um erro.',
        loading: 'Carregando...'
      }
    },
  },
};