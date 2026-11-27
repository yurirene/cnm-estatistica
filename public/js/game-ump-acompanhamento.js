(function () {
  'use strict';

  const root = document.getElementById('painel-game-ump');
  if (!root) return;

  const $ = (sel, el = document) => el.querySelector(sel);
  const esc = (t) => String(t ?? '').replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
  }[c]));
  const dataBr = (iso) => (iso || '').split('-').reverse().join('/');
  const porCento = (v) => `${Math.round(v * 100)}%`;
  const NOME_TIPO = { sinodal: 'Sinodais', federacao: 'Federações' };
  const NOME_STATUS = {
    andamento: 'Em andamento',
    definitivo: 'Resultado definitivo',
    futuro: 'Não iniciado'
  };

  const inicialEl = document.getElementById('game-ump-inicial');
  let painel = {};
  try {
    painel = JSON.parse(inicialEl ? inicialEl.textContent : '{}');
  } catch (e) {
    painel = {};
  }
  const urlDados = root.dataset.url;
  const estado = {
    tipo: painel.tipo || 'sinodal',
    periodo: painel.periodo || painel.ano_atual || 2026,
    liga: '',
    busca: '',
    criterio: '',
    carregando: false
  };
  let exportavel = [];

  function ligas() {
    return painel.ligas || [];
  }

  function nomeLiga(id) {
    return (ligas().find((l) => l.id === id) || {}).label || id;
  }

  function faixaLiga(id) {
    return (ligas().find((l) => l.id === id) || {}).faixa || '';
  }

  function orgs() {
    return painel.organizacoes || [];
  }

  function emCiclo() {
    return estado.periodo === 'ciclo';
  }

  function statusAno(ano) {
    return (painel.status_exercicios || {})[ano] || 'futuro';
  }

  function maximoAtual() {
    return emCiclo() ? (painel.teto_ciclo || 1) : (painel.teto_ano || 1);
  }

  function pontosOrg(org) {
    return emCiclo() ? (org.pontos_ciclo || 0) : (org.pontos_ano || 0);
  }

  function visivel(org) {
    const busca = estado.busca.toLocaleLowerCase('pt-BR');
    const nomeOk = !busca || (org.nome || '').toLocaleLowerCase('pt-BR').includes(busca)
      || (org.sigla || '').toLocaleLowerCase('pt-BR').includes(busca);
    const ligaOk = !estado.liga || org.liga === estado.liga;
    const criterioOk = !estado.criterio || !(org.pilares || []).find((p) => p.id === estado.criterio && p.situacao === 'atingido');
    return nomeOk && ligaOk && criterioOk;
  }

  function botoes(el, opcoes, atual, aoEscolher) {
    el.innerHTML = opcoes.map(([v, t]) => (
      `<button type="button" class="btn btn-sm" data-v="${v}" aria-pressed="${String(v) === String(atual)}">${t}</button>`
    )).join('');
    el.onclick = (e) => {
      const b = e.target.closest('button');
      if (!b) return;
      aoEscolher(b.dataset.v);
    };
  }

  async function carregar(tipo, periodo) {
    if (estado.carregando) return;
    estado.carregando = true;
    root.classList.add('is-loading');
    try {
      const qs = new URLSearchParams({
        tipo,
        periodo: String(periodo)
      });
      const res = await fetch(`${urlDados}?${qs.toString()}`, {
        headers: { Accept: 'application/json' }
      });
      if (!res.ok) throw new Error('Falha ao carregar dados');
      painel = await res.json();
      estado.tipo = painel.tipo;
      estado.periodo = painel.periodo;
      estado.criterio = '';
      render(false);
    } catch (err) {
      console.error(err);
    } finally {
      estado.carregando = false;
      root.classList.remove('is-loading');
    }
  }

  function situacao() {
    const el = $('#game-situacao');
    const s = painel.situacao || {};
    const prox = s.proximo_prazo;
    const item = (t, v, n, cls = '') => (
      `<dl class="${cls}"><dt>${esc(t)}</dt><dd>${v}${n ? `<small>${esc(n)}</small>` : ''}</dd></dl>`
    );
    const proximo = prox
      ? item('Próximo prazo', prox.dias === 0 ? 'Hoje' : `${prox.dias} dias`, `${dataBr(prox.data)}: ${prox.titulo}`, 'destaque')
      : '';

    if (s.modo === 'ciclo') {
      el.innerHTML = item('Exercícios definitivos', `${s.exercicios_definitivos || 0} de 4`, s.exercicios_definitivos_label || '')
        + item('Aproveitamento de 55% ou mais', s.exercicios_definitivos ? `${s.no_ritmo} de ${s.total_orgs}` : 'Sem dados', 'Considera só resultados definitivos')
        + item('Pontuação máxima no ciclo', `${s.teto_ciclo} pontos`, s.faixa_100 ? `100% de desconto a partir de ${s.faixa_100}` : '')
        + proximo;
      return;
    }

    let media = item('Média parcial', 'Sem dados', 'O exercício ainda não começou');
    if (s.status !== 'futuro') {
      const rotulo = s.status === 'definitivo' ? 'Média' : 'Média parcial';
      media = item(rotulo, `${s.media ?? 0} de ${s.maximo}`, `${s.total_orgs} ${NOME_TIPO[estado.tipo] || ''}`);
    }
    el.innerHTML = item(`Exercício ${s.ano}`, s.status_label || NOME_STATUS[s.status] || '', s.nota || '')
      + media
      + proximo;
  }

  function pista() {
    const el = $('#game-pista');
    const max = maximoAtual();
    $('#game-pista-titulo').textContent = emCiclo()
      ? 'Pontos do ciclo rumo às faixas de desconto'
      : `Onde está cada ${estado.tipo === 'sinodal' ? 'Sinodal' : 'Federação'}`;
    $('#game-pista-ajuda').textContent = emCiclo()
      ? 'Soma acumulada no ciclo. As linhas douradas marcam as faixas de desconto configuradas.'
      : 'Cada ponto é uma organização. Selecione para abrir a pontuação detalhada. A liga não altera pontos nem desconto.';

    const faixas = painel.faixas_desconto || {};
    const marcasDesconto = emCiclo()
      ? [50, 75, 100].map((pct) => {
        const pts = Number(faixas[pct] || 0);
        return pts > 0 ? { pct: (pts / max) * 100, label: `${pct}%`, pts } : null;
      }).filter(Boolean)
      : [];

    const largura = Math.max(640, el.clientWidth) - 178;
    const raias = ligas().map((l) => {
      const lista = orgs().filter((o) => o.liga === l.id)
        .map((o) => ({ o, pts: pontosOrg(o) }));
      const postos = [];
      let linhas = 1;
      let marcas = '';
      lista.sort((a, b) => a.pts - b.pts).forEach((x) => {
        const ideal = (x.pts / max) * largura;
        let lugar = null;
        for (let k = 0; !lugar && k < 200; k++) {
          const px = ideal + (k % 2 ? 1 : -1) * Math.ceil(k / 2) * 19;
          if (px < 0 || px > largura) continue;
          for (let linha = 0; linha < 5; linha++) {
            if (!postos.some((q) => q.linha === linha && Math.abs(q.px - px) < 18.5)) {
              lugar = { px, linha };
              break;
            }
          }
        }
        lugar = lugar || { px: Math.max(0, Math.min(largura, ideal)), linha: 0 };
        postos.push(lugar);
        linhas = Math.max(linhas, lugar.linha + 1);
        marcas += `<button type="button" class="game-marca" data-org="${esc(x.o.id)}" style="left:${(lugar.px / largura) * 100}%;top:${12 + lugar.linha * 20}px" title="${esc(x.o.nome)}: ${x.pts} pontos" aria-label="${esc(x.o.nome)}, ${x.pts} de ${max} pontos"></button>`;
      });

      const guias = emCiclo()
        ? marcasDesconto.map((z) => `<i class="zona" style="left:${z.pct}%;right:0"></i>`).join('')
        : [0, 25, 50, 75, 100].map((v) => `<i style="left:${v}%"></i>`).join('');

      return `<div class="game-raia liga-${esc(l.id)}"><div class="game-raia-nome"><strong>${esc(l.label)}</strong><span>${esc(l.faixa)}</span></div><div class="game-trilho" style="height:${Math.max(66, linhas * 20 + 22)}px">${guias}${marcas}</div></div>`;
    }).join('');

    let eixo;
    if (emCiclo()) {
      eixo = marcasDesconto.map((z) => `<span style="left:${z.pct}%">${z.pts} pts<br>${z.label} desc.</span>`).join('')
        + `<span style="left:0">0</span><span style="left:100%">${max}</span>`;
    } else {
      eixo = [0, 0.25, 0.5, 0.75, 1].map((f) => `<span style="left:${f * 100}%">${Math.round(max * f)}${f === 1 ? ' pts' : ''}</span>`).join('');
    }

    el.innerHTML = `<div class="game-pista-grade">${raias}<div class="game-eixo"><div>${eixo}</div></div></div>`;
  }

  function adesao() {
    const bloco = $('#game-adesao-bloco');
    const col = $('#game-adesao-col');
    const prazosCol = $('#game-prazos-col');
    const esconder = emCiclo();
    bloco.hidden = esconder;
    col.hidden = esconder;
    if (esconder) {
      prazosCol.classList.remove('col-lg-5');
      prazosCol.classList.add('col-lg-12');
      return;
    }
    prazosCol.classList.add('col-lg-5');
    prazosCol.classList.remove('col-lg-12');

    const lista = painel.adesao || [];
    $('#game-adesao').innerHTML = lista.map((c) => {
      const n = c.total || 1;
      return `<li><button type="button" data-c="${esc(c.id)}" aria-pressed="${estado.criterio === c.id}">
        <span><strong>${esc(c.nome)}</strong><small>até ${c.max} pontos</small></span>
        <span class="game-barra" aria-hidden="true"><b style="width:${(c.atingido / n) * 100}%"></b><em style="width:${(c.parcial / n) * 100}%"></em></span>
        <span class="game-numero">${c.atingido} de ${n}${c.parcial ? `<small>+${c.parcial} parcial</small>` : ''}</span>
      </button></li>`;
    }).join('') || '<li class="text-muted">Sem critérios para este exercício.</li>';
  }

  function prazos() {
    const todos = painel.prazos || [];
    const i = Math.max(0, todos.findIndex((x) => !x.passou));
    const fatia = todos.slice(Math.max(0, i - 1), i + 4);
    $('#game-prazos').innerHTML = fatia.map((x) => {
      const prox = x === todos[i] && !x.passou;
      return `<li class="${x.passou ? 'passou' : prox ? 'proximo' : ''}"><time datetime="${esc(x.data)}">${dataBr(x.data)}</time><span>${esc(x.titulo)}<small>${x.passou ? 'Encerrado' : esc(x.nota)}</small></span></li>`;
    }).join('') || '<li class="text-muted">Sem prazos cadastrados.</li>';
  }

  function celula(pilar) {
    const texto = pilar.situacao === 'pendente' ? '…' : pilar.pontos;
    const titulo = `${pilar.label}: ${pilar.medida || ''}`;
    return `<span class="game-ponto ${esc(pilar.situacao)}" title="${esc(titulo)}">${texto}</span>`;
  }

  function tabela() {
    const el = $('#game-tabela');
    const crits = painel.criterios || [];
    exportavel = [];
    $('#game-legenda').hidden = emCiclo();

    const nomeBtn = (o) => `<button type="button" class="game-nome" data-org="${esc(o.id)}">${esc(o.nome)}</button>${o.sinodal ? `<small class="d-block text-muted">${esc(o.sinodal)}</small>` : ''}`;
    const grupo = (liga, n) => `<tr class="game-grupo liga-${esc(liga)}"><th colspan="${n}" scope="colgroup">${esc(nomeLiga(liga))}<span>${esc(faixaLiga(liga))}</span></th></tr>`;

    let cab;
    let corpo = '';

    if (emCiclo()) {
      $('#game-tabela-titulo').textContent = 'Acumulado no ciclo';
      $('#game-tabela-ajuda').textContent = 'Ordem apenas para acompanhamento: o edital prevê classificação anual (art. 30).';
      cab = ['Organização', ...(painel.exercicios || []), 'Ciclo', 'Desconto projetado'];
      ligas().forEach((l) => {
        const linhas = orgs().filter((o) => o.liga === l.id && visivel(o))
          .sort((a, b) => b.pontos_ciclo - a.pontos_ciclo);
        if (!linhas.length) return;
        corpo += grupo(l.id, cab.length);
        linhas.forEach((o) => {
          const anos = (painel.exercicios || []).map((a) => {
            const info = (o.anos || {})[a];
            if (!info || info.pontos === null || info.pontos === undefined) {
              return ['', '<span aria-label="não iniciado">–</span>'];
            }
            const st = info.status;
            const extra = st === 'definitivo' ? '' : `<small>${st === 'andamento' ? 'parcial' : st}</small>`;
            return [info.pontos, `${info.pontos}${extra}`];
          });
          corpo += `<tr><td class="text-left">${nomeBtn(o)}</td>${anos.map((a) => `<td>${a[1]}</td>`).join('')}<td class="game-total">${o.pontos_ciclo}</td><td>${o.desconto_projetado ? `${o.desconto_projetado}%` : 'Sem desconto'}</td></tr>`;
          exportavel.push([o.nome, nomeLiga(o.liga), ...anos.map((a) => a[0]), o.pontos_ciclo, o.desconto_projetado]);
        });
      });
      exportavel.unshift(['Organização', 'Liga', ...(painel.exercicios || []), 'Ciclo', 'Desconto']);
    } else {
      const st = statusAno(estado.periodo);
      $('#game-tabela-titulo').textContent = `${st === 'definitivo' ? 'Classificação' : 'Posições parciais'} ${estado.periodo}`;
      $('#game-tabela-ajuda').textContent = 'Desempate já aplicado no placar oficial (art. 31).';
      cab = ['Posição', 'Organização', ...crits.map((c) => c.curto), 'Total', 'Selos'];
      ligas().forEach((l) => {
        const linhas = orgs().filter((o) => o.liga === l.id && visivel(o));
        if (!linhas.length) return;
        corpo += grupo(l.id, cab.length);
        linhas.forEach((o) => {
          const pilares = crits.map((c) => (o.pilares || []).find((p) => p.id === c.id) || {
            id: c.id, label: c.label, pontos: 0, situacao: 'pendente', medida: ''
          });
          const selos = (o.selos || []).map((s) => `<span class="game-selo">${esc(s.label)}</span>`).join('') || '–';
          corpo += `<tr><td>${o.posicao || '—'}º</td><td class="text-left">${nomeBtn(o)}</td>${pilares.map((p) => `<td>${celula(p)}</td>`).join('')}
            <td class="game-total">${o.pontos_ano}</td><td>${selos}</td></tr>`;
          exportavel.push([o.posicao, o.nome, nomeLiga(o.liga), ...pilares.map((p) => p.pontos), o.pontos_ano, (o.selos || []).map((s) => s.label).join(', ')]);
        });
      });
      exportavel.unshift(['Posição', 'Organização', 'Liga', ...crits.map((c) => c.label), 'Total', 'Selos']);
    }

    el.innerHTML = `<thead><tr>${cab.map((c) => `<th scope="col" class="${c === 'Organização' ? 'text-left' : ''}">${esc(c)}</th>`).join('')}</tr></thead><tbody>${corpo ||
      `<tr><td colspan="${cab.length}" class="text-center text-muted py-5">Nenhuma organização com esses filtros.</td></tr>`}</tbody>`;

    const aviso = $('#game-filtro-ativo');
    aviso.hidden = !estado.criterio;
    if (estado.criterio) {
      const nome = (painel.criterios || []).find((c) => c.id === estado.criterio)?.label || estado.criterio;
      aviso.innerHTML = `Mostrando quem não completou ${esc(nome)}. <button type="button" class="btn btn-link btn-sm p-0">Mostrar todas</button>`;
      aviso.querySelector('button').onclick = () => {
        estado.criterio = '';
        adesao();
        tabela();
      };
    }
  }

  function abrirDetalhe(id) {
    const org = orgs().find((o) => o.id === id);
    if (!org) return;
    const ano = emCiclo() ? (painel.ano_atual || estado.periodo) : estado.periodo;
    const st = statusAno(ano);
    const pilares = org.pilares || [];
    const maxAno = painel.teto_ano || 1;
    const maxCiclo = painel.teto_ciclo || 1;
    const faixas = painel.faixas_desconto || {};

    const criteriosHtml = pilares.map((i) => `<div class="game-criterio">
      <h3>${esc(i.label)}</h3><span class="game-ponto ${esc(i.situacao)}">${i.pontos} / ${i.max}</span>
      <span class="game-barra" aria-hidden="true"><b style="width:${Math.min(1, i.progresso || 0) * 100}%"></b></span>
      <p>${esc(i.medida || '')}</p>
    </div>`).join('') || '<p class="text-muted">Sem critérios neste exercício.</p>';

    const marcas = [50, 75, 100].map((pct) => {
      const pts = Number(faixas[pct] || 0);
      if (!pts) return '';
      return `<i style="left:${(pts / maxCiclo) * 100}%"><span>${pct}%</span><em>${pts} pts</em></i>`;
    }).join('');

    const dlg = $('#game-detalhe');
    dlg.innerHTML = `
      <div class="game-d-topo">
        <div class="d-flex justify-content-between align-items-start" style="gap:12px">
          <div>
            <h2 id="game-detalhe-nome">${esc(org.nome)}</h2>
            <p>${esc(nomeLiga(org.liga))}, ${org.socios} sócios ativos${org.sinodal ? `. ${esc(org.sinodal)}` : ''}</p>
          </div>
          <button type="button" class="btn btn-sm btn-outline-light" data-fechar>Fechar</button>
        </div>
        <div class="game-d-total">${emCiclo() ? org.pontos_ciclo : org.pontos_ano}<small>de ${emCiclo() ? maxCiclo : maxAno} pontos${emCiclo() ? ' no ciclo' : ` em ${ano}`}${org.posicao ? `, ${org.posicao}º na liga` : ''} (${(NOME_STATUS[st] || st).toLowerCase()})</small></div>
      </div>
      <div class="game-d-corpo">
        ${(org.selos || []).length ? `<p>${(org.selos || []).map((s) => `<span class="game-selo">${esc(s.label)}</span>`).join(' ')}</p>` : ''}
        <section><h3 class="h5">Critérios de ${ano}</h3>${criteriosHtml}</section>
        <section>
          <h3 class="h5">Caminho até o Congresso Nacional 2030</h3>
          <div class="game-regua" role="img" aria-label="${org.pontos_ciclo} pontos no ciclo de ${maxCiclo}">
            <span class="garantido" style="width:${(org.pontos_ciclo / maxCiclo) * 100}%"></span>
            ${marcas}
          </div>
          <p><strong>${org.pontos_ciclo}</strong> pontos no ciclo. Desconto projetado: <strong>${org.desconto_projetado || 0}%</strong>.</p>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead><tr><th>Exercício</th>${(painel.exercicios || []).map((a) => `<th>${a}</th>`).join('')}</tr></thead>
              <tbody><tr><th scope="row">Pontos</th>${(painel.exercicios || []).map((a) => {
                const info = (org.anos || {})[a];
                return `<td>${info && info.pontos != null ? info.pontos : '–'}<small class="d-block text-muted">de ${info ? info.max : '—'}</small></td>`;
              }).join('')}</tr></tbody>
            </table>
          </div>
          <p class="text-muted small mb-0 mt-2">Expectativa de benefício, sem direito adquirido (arts. 35 e 36).</p>
        </section>
      </div>`;
    dlg.showModal();
    dlg.scrollTop = 0;
  }

  function exportar() {
    const campo = (v) => {
      let t = String(v ?? '');
      if (/^[=+\-@]/.test(t)) t = `'${t}`;
      return `"${t.replace(/"/g, '""')}"`;
    };
    const blob = new Blob(['\uFEFF' + exportavel.map((l) => l.map(campo).join(';')).join('\r\n')], { type: 'text/csv;charset=utf-8' });
    const a = Object.assign(document.createElement('a'), {
      href: URL.createObjectURL(blob),
      download: `game-ump-${estado.tipo}-${estado.periodo}.csv`
    });
    a.click();
    setTimeout(() => URL.revokeObjectURL(a.href), 1000);
  }

  function montarFiltros() {
    botoes($('#game-tipos'), Object.entries(NOME_TIPO), estado.tipo, (v) => {
      if (v === estado.tipo) return;
      carregar(v, estado.periodo);
    });
    const periodos = [...(painel.exercicios || []).map((a) => [a, String(a)]), ['ciclo', 'Ciclo completo']];
    botoes($('#game-periodos'), periodos, estado.periodo, (v) => {
      const periodo = v === 'ciclo' ? 'ciclo' : Number(v);
      if (String(periodo) === String(estado.periodo)) return;
      carregar(estado.tipo, periodo);
    });
    const liga = $('#game-liga');
    const atual = estado.liga;
    liga.innerHTML = '<option value="">Todas as ligas</option>'
      + ligas().map((l) => `<option value="${esc(l.id)}">${esc(l.label)}</option>`).join('');
    liga.value = atual;
  }

  function render(atualizarFiltros = true) {
    if (atualizarFiltros) montarFiltros();
    situacao();

    const futuro = !emCiclo() && statusAno(estado.periodo) === 'futuro';
    $('#game-vazio').hidden = !futuro;
    $('#game-conteudo').hidden = futuro;
    if (futuro) {
      const crits = (painel.criterios || []).map((c) => `${c.label} (${c.max})`).join(', ');
      $('#game-vazio-corpo').innerHTML = `<h2 class="h3">O exercício ${estado.periodo} ainda não começou</h2><p class="text-muted mb-0">Valerão até ${painel.teto_ano} pontos: ${esc(crits)}.</p>`;
      return;
    }
    pista();
    adesao();
    prazos();
    tabela();
  }

  $('#game-liga').onchange = (e) => {
    estado.liga = e.target.value;
    tabela();
  };
  $('#game-busca').oninput = (e) => {
    estado.busca = e.target.value;
    tabela();
  };
  $('#game-exportar').onclick = exportar;
  $('#game-adesao').onclick = (e) => {
    const b = e.target.closest('button[data-c]');
    if (!b) return;
    estado.criterio = estado.criterio === b.dataset.c ? '' : b.dataset.c;
    adesao();
    tabela();
  };
  document.addEventListener('click', (e) => {
    const alvo = e.target.closest('[data-org]');
    if (alvo && root.contains(alvo)) abrirDetalhe(alvo.dataset.org);
    const dlg = $('#game-detalhe');
    if (e.target.closest('[data-fechar]') || e.target === dlg) dlg.close();
  });

  render(true);
  let espera;
  window.addEventListener('resize', () => {
    clearTimeout(espera);
    espera = setTimeout(() => {
      if (!$('#game-conteudo').hidden) pista();
    }, 150);
  });
})();
