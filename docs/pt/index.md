# Debug Kit

DebugKit oferece uma barra de ferramentas de depuração e ferramentas avançadas para aplicações CakePHP. Ele permite visualizar rapidamente dados de configuração, mensagens de log, consultas SQL e informações de tempo de execução.

::: warning
DebugKit deve ser usado apenas em ambientes locais de desenvolvimento para um único usuário. Evite utilizá-lo em ambientes compartilhados, de homologação ou próximos de produção.
:::

## Instalação

No diretório raiz da aplicação, execute:

```bash
php composer.phar require --dev cakephp/debug_kit:"^5.0"
```

Depois carregue o plugin:

```bash
bin/cake plugin load DebugKit --only-debug
```

## Armazenamento do DebugKit

Por padrão, o DebugKit usa um banco SQLite no diretório `tmp` da aplicação para armazenar os dados dos painéis. Se quiser usar outro banco, defina uma conexão `debug_kit` em `config/app.php`.

## Uso da barra de ferramentas

A toolbar do DebugKit é exibida ao clicar no ícone do CakePHP no canto inferior direito do navegador.

Cada painel mostra uma parte diferente da aplicação:

* **Cache** mostra o uso de cache e permite limpá-lo.
* **Environment** exibe variáveis de ambiente relacionadas a PHP e CakePHP.
* **History** mostra requisições anteriores e permite recarregar seus dados.
* **Include** exibe os arquivos incluídos por tipo.
* **Log** mostra as entradas de log da requisição.
* **Request** exibe dados da requisição atual, rota e cookies.
* **Session** exibe o conteúdo da sessão.
* **Sql Logs** mostra logs SQL por conexão.
* **Timer** exibe timers criados com `DebugKit\\DebugTimer` e dados de memória com `DebugKit\\DebugMemory`.
* **Variables** exibe variáveis de view definidas no controller.

## Usando o painel History

O painel History permite revisar dados de requisições anteriores, incluindo erros e redirecionamentos.

![Screenshot do painel History](/history-panel.png)

<video controls preload="metadata" src="/history-panel-use.mp4"></video>

## Desenvolvendo seus próprios painéis

Você pode criar painéis personalizados para expor dados específicos da sua aplicação.

### Criando uma classe de painel

As classes devem ser colocadas em `src/Panel` e estender `DebugKit\DebugPanel`:

```php
namespace App\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    // ...
}
```

### Callbacks

Por padrão os painéis se inscrevem nos eventos `Controller.initialize` e `Controller.shutdown`. Se precisar de eventos adicionais, implemente `implementedEvents()`.

### Elementos do painel

Cada painel precisa de um elemento de view. O nome deve ser a versão underscore do nome da classe, por exemplo `session_panel.php`.

### Títulos e elementos personalizados

Você pode sobrescrever:

* `title()` para definir o título na toolbar.
* `elementName()` para escolher qual elemento renderizar.

### Métodos de hook

* `shutdown(Event $event)` coleta e prepara os dados do painel.
* `summary()` retorna um resumo curto para a toolbar recolhida.
* `data()` retorna dados serializáveis usados pelo elemento.

### Painéis em outros plugins

```php
namespace MyPlugin\Panel;

use DebugKit\DebugPanel;

class MyCustomPanel extends DebugPanel
{
    public string $plugin = 'MyPlugin';
}
```

Para registrar um painel do aplicativo ou de plugin:

```php
Configure::write(
    'DebugKit.panels',
    array_merge(Configure::read('DebugKit.panels'), ['MyCustomPanel'])
);
```
