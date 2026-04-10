<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Designer — {{ $report->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        html,body{height:100%;overflow:hidden;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;font-size:13px;color:#1e293b;background:#0f172a}
        input,select,textarea,button{font-family:inherit}

        /* ── APP SHELL ─────────────────────────────────────────────────── */
        #app{display:flex;flex-direction:column;height:100vh}

        /* TOP BAR */
        #topBar{height:46px;background:#1e3a5c;display:flex;align-items:center;padding:0 14px;gap:10px;flex-shrink:0;z-index:200}
        .tb-back{color:#94a3b8;font-size:.75rem;text-decoration:none;display:flex;align-items:center;gap:4px;padding:4px 8px;border-radius:5px;border:1px solid rgba(255,255,255,.15)}
        .tb-back:hover{background:rgba(255,255,255,.1);color:#fff}
        #topBar h1{color:#fff;font-size:.88rem;font-weight:600;flex:1}
        .rname{color:#60a5fa;font-size:.78rem;opacity:.85;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .btn-tb{padding:6px 14px;border:none;border-radius:6px;cursor:pointer;font-size:.78rem;font-weight:600;display:flex;align-items:center;gap:5px;transition:.15s}
        .btn-tb.save{background:#16a34a;color:#fff}
        .btn-tb.save:hover{background:#15803d}
        .btn-tb.save.saving{background:#0369a1}
        .btn-tb.save.saved{background:#059669}
        .btn-tb.prev{background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2)}
        .btn-tb.prev:hover{background:rgba(255,255,255,.2)}

        /* BODY */
        #body{display:flex;flex:1;overflow:hidden}

        /* ── SIDEBAR ──────────────────────────────────────────────────── */
        #sb{width:272px;min-width:272px;background:#fff;border-right:1px solid #e2e8f0;display:flex;flex-direction:column;overflow:hidden}
        #sbTabs{display:flex;background:#f8fafc;border-bottom:1px solid #e2e8f0;flex-shrink:0}
        .st{flex:1;padding:8px 2px;font-size:.58rem;font-weight:700;cursor:pointer;text-align:center;color:#64748b;border-bottom:2.5px solid transparent;text-transform:uppercase;letter-spacing:.3pt;transition:.12s}
        .st.on{color:#2563eb;border-bottom-color:#2563eb;background:#fff}
        .st:hover{color:#2563eb;background:#f0f7ff}
        #sbBody{flex:1;overflow-y:auto;padding:11px}

        /* SIDEBAR CONTROLS */
        .ss{margin-bottom:13px}
        .sl{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.4pt;color:#64748b;margin-bottom:5px;display:flex;justify-content:space-between;align-items:center}
        .sr{display:flex;gap:5px;margin-bottom:5px;align-items:flex-start}
        .sc{flex:1;min-width:0}
        .sc label{display:block;font-size:.6rem;color:#94a3b8;margin-bottom:2px}
        .si{width:100%;font-size:.72rem;padding:4px 6px;border:1px solid #e2e8f0;border-radius:4px;background:#fff;color:#1e293b}
        .si:focus{outline:none;border-color:#2563eb}
        .ssel{width:100%;font-size:.72rem;padding:4px 6px;border:1px solid #e2e8f0;border-radius:4px;background:#fff;color:#1e293b}
        .sclr{width:30px;height:24px;border:1px solid #e2e8f0;border-radius:4px;padding:2px;cursor:pointer;background:none}
        .sdiv{height:1px;background:#f1f5f9;margin:10px -11px}
        .stog{display:flex;justify-content:space-between;align-items:center;padding:3px 0;border-bottom:.5px solid #f8fafc}
        .stog label{font-size:.72rem;color:#374151}
        .stog input[type=checkbox]{accent-color:#2563eb;cursor:pointer;width:13px;height:13px}
        .sbtn{padding:4px 8px;border:1px solid #e2e8f0;border-radius:4px;cursor:pointer;font-size:.68rem;font-weight:500;background:#fff;color:#1e293b;transition:.12s;display:inline-flex;align-items:center;gap:3px;white-space:nowrap}
        .sbtn:hover{background:#f1f5f9}
        .sbtn.pp{background:#2563eb;color:#fff;border-color:#2563eb}
        .sbtn.pg{background:#16a34a;color:#fff;border-color:#16a34a}
        .sbtn.pd{color:#dc2626;border-color:#fca5a5;font-size:.62rem}
        .sbtn.pd:hover{background:#fef2f2}

        /* LAYOUT CARDS */
        .lc-grid{display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:8px}
        .lc{padding:8px 5px;border:1.5px solid #e2e8f0;border-radius:6px;cursor:pointer;text-align:center;background:#fff;transition:.12s}
        .lc:hover{border-color:#2563eb;background:#f0f7ff}
        .lc.on{border-color:#2563eb;background:#eff6ff}
        .lc i{font-size:1.1rem;display:block;margin-bottom:3px;color:#94a3b8}
        .lc.on i{color:#2563eb}
        .lc .lcl{font-size:.62rem;font-weight:600;color:#374151}
        .lc.on .lcl{color:#2563eb}

        /* HEADER ELEMENTS */
        .logo-drop{border:2px dashed #dde3ec;border-radius:6px;padding:9px;text-align:center;cursor:pointer;background:#fafafa;min-height:60px;display:flex;align-items:center;justify-content:center;transition:.2s}
        .logo-drop:hover,.logo-drop.dg{border-color:#2563eb;background:#eff6ff}
        .logo-drop img{max-height:48px;max-width:140px;object-fit:contain}
        .pal{display:grid;grid-template-columns:repeat(3,1fr);gap:3px;margin-bottom:7px}
        .pb{padding:5px 3px;border:1px solid #e2e8f0;border-radius:4px;cursor:pointer;font-size:.6rem;text-align:center;background:#f8fafc;color:#374151;transition:.12s;line-height:1.3}
        .pb:hover{border-color:#2563eb;background:#eff6ff;color:#2563eb}
        .pb svg{display:block;margin:0 auto 2px}
        .el-row{display:flex;align-items:center;gap:4px;padding:4px 6px;border:1px solid #e2e8f0;border-radius:4px;margin-bottom:3px;cursor:pointer;background:#fff;font-size:.68rem;transition:.1s}
        .el-row:hover,.el-row.on{border-color:#2563eb;background:#eff6ff}
        .el-badge{font-size:.55rem;background:#f1f5f9;padding:1px 5px;border-radius:10px;color:#64748b;min-width:40px;text-align:center;flex-shrink:0}
        .ped{background:#f0f7ff;border:1px solid #bfdbfe;border-radius:5px;padding:7px;margin-top:3px;margin-bottom:4px}

        /* COLOUR PRESETS */
        .cpr{display:flex;flex-wrap:wrap;gap:4px;margin-bottom:5px}
        .cpill{display:inline-flex;align-items:center;gap:3px;cursor:pointer;padding:3px 7px;border:1px solid #e2e8f0;border-radius:20px;font-size:.62rem;background:#fff;transition:.1s}
        .cpill:hover{border-color:#2563eb;background:#eff6ff}

        /* COLUMN STYLE EDITOR */
        .col-sel-info{background:#eff6ff;border:1px solid #bfdbfe;border-radius:5px;padding:8px;margin-bottom:8px}
        .col-prop-grid{display:grid;grid-template-columns:1fr 1fr;gap:5px}

        /* ── CANVAS ─────────────────────────────────────────────────── */
        #cw{flex:1;background:#2d3748;overflow:auto;display:flex;flex-direction:column;align-items:center;padding:24px;position:relative}
        #pw{transform-origin:top center;transition:transform .15s;margin-bottom:40px}
        #paper{background:#fff;box-shadow:0 4px 32px rgba(0,0,0,.4);position:relative;font-family:Arial,Helvetica,sans-serif;font-size:8pt;color:#000;user-select:none}
        #paper *{box-sizing:border-box}

        /* ZONES */
        #zh{position:relative;overflow:visible}
        #zh.editing{border-bottom:2px dashed #93c5fd;background:#fafcff}
        .zlbl{position:absolute;top:2px;left:4px;font-size:.5rem;font-weight:700;text-transform:uppercase;color:#93c5fd;pointer-events:none;z-index:20;letter-spacing:.5pt}
        #zp{padding:3pt 10pt;font-size:6.5pt;color:#aaa;font-style:italic}
        #zd{padding:0 10pt 8pt}
        #zf{padding:3pt 10pt;font-size:6.5pt;display:flex;justify-content:space-between;color:#aaa}

        /* BLANK STATE */
        #blankState{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:30pt;gap:8px}
        .bs-icon{font-size:2rem;color:#e2e8f0}
        .bs-title{font-size:9pt;font-weight:bold;color:#94a3b8}
        .bs-sub{font-size:7pt;color:#cbd5e0;text-align:center;max-width:260px;line-height:1.5}

        /* HEADER ELEMENTS ON CANVAS */
        .hel{position:absolute;cursor:move;border:1.5px solid transparent;border-radius:2px;user-select:none;z-index:5}
        .hel:hover{border-color:#3b82f6!important;z-index:10}
        .hel.on{border-color:#2563eb!important;background:rgba(37,99,235,.04);z-index:10}
        .hel .hd{position:absolute;top:-9px;right:-9px;width:17px;height:17px;background:#ef4444;color:#fff;border-radius:50%;font-size:9px;display:none;align-items:center;justify-content:center;cursor:pointer;z-index:15;line-height:1}
        .hel:hover .hd,.hel.on .hd{display:flex}

        /* DATA TABLE ON CANVAS */
        .dt{width:100%;border-collapse:collapse;font-size:7pt}
        .dt th{cursor:pointer;position:relative;transition:.1s}
        .dt th:hover{filter:brightness(90%)}
        .dt th.col-sel{outline:2px solid #f59e0b;outline-offset:-2px;z-index:5}
        .dt td{cursor:default}
        .col-resize-handle{position:absolute;right:0;top:0;bottom:0;width:5px;cursor:col-resize;background:transparent;z-index:10}
        .col-resize-handle:hover{background:rgba(255,255,255,.3)}

        /* ZOOM BAR */
        #zb{position:sticky;bottom:0;left:50%;transform:translateX(-50%);background:rgba(255,255,255,.95);border-radius:8px;padding:4px 8px;display:flex;align-items:center;gap:6px;box-shadow:0 2px 10px rgba(0,0,0,.3);width:fit-content;align-self:center;margin-top:8px}
        .zbt{width:26px;height:26px;border:1px solid #dde3ec;border-radius:4px;cursor:pointer;background:#fff;font-size:15px;display:flex;align-items:center;justify-content:center}
        .zbt:hover{background:#f1f5f9}
        #zlbl{font-size:.72rem;min-width:36px;text-align:center;color:#1e293b;font-weight:500}

        /* TOAST */
        #toast{position:fixed;bottom:16px;left:50%;transform:translateX(-50%);background:#1e3a5c;color:#fff;padding:7px 18px;border-radius:20px;font-size:.75rem;font-weight:500;opacity:0;transition:opacity .3s;pointer-events:none;z-index:9999}
        #toast.on{opacity:1}

        /* ── WIZARD MODAL ───────────────────────────────────────────── */
        .wiz-overlay{position:fixed;inset:0;background:rgba(0,0,0,.65);z-index:500;display:flex;align-items:center;justify-content:center}
        .wiz-box{background:#fff;border-radius:12px;width:640px;max-width:95vw;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.4);overflow:hidden}
        .wiz-top{background:#1e3a5c;padding:14px 18px;display:flex;align-items:center;gap:8px}
        .wiz-top h2{color:#fff;font-size:.9rem;font-weight:600;flex:1}
        .wiz-top .wt-sub{color:#94a3b8;font-size:.72rem}
        .wiz-body{padding:18px;overflow-y:auto;flex:1}
        .wiz-foot{padding:12px 18px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:8px;background:#f8fafc}
        .wiz-step{display:none}
        .wiz-step.on{display:block}
        .wiz-step-title{font-size:.82rem;font-weight:600;color:#1e293b;margin-bottom:4px}
        .wiz-step-sub{font-size:.72rem;color:#64748b;margin-bottom:12px}

        /* COL CHECKBOX GRID */
        .col-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:6px;margin-bottom:10px}
        .col-cb{display:flex;align-items:center;gap:7px;padding:7px 10px;border:1.5px solid #e2e8f0;border-radius:6px;cursor:pointer;transition:.12s;background:#fff;font-size:.75rem}
        .col-cb:hover{border-color:#2563eb;background:#eff6ff}
        .col-cb input[type=checkbox]{accent-color:#2563eb;cursor:pointer;width:14px;height:14px;flex-shrink:0}
        .col-cb input[type=radio]{accent-color:#2563eb;cursor:pointer;width:14px;height:14px;flex-shrink:0}
        .col-cb.checked{border-color:#2563eb;background:#eff6ff}
        .col-cb .ct{display:inline-block;padding:1px 5px;border-radius:3px;font-size:.58rem;font-weight:700;margin-left:auto}
        .col-cb .ct.n{background:#d1fae5;color:#065f46}
        .col-cb .ct.t{background:#dbeafe;color:#1e40af}

        /* TOTAL COLUMNS TOGGLE */
        .tot-row{display:flex;align-items:center;gap:8px;padding:5px 8px;border:1px solid #e2e8f0;border-radius:5px;margin-bottom:4px;font-size:.73rem}
        .tot-row input{accent-color:#2563eb}
        .tot-row .lbl{flex:1}
        .tot-row .tc{font-size:.58rem;padding:1px 5px;border-radius:3px;background:#d1fae5;color:#065f46;font-weight:700}
    </style>
</head>
<body>
<div id="app">

    <!-- TOP BAR -->
    <div id="topBar">
        <a href="{{ route('admin.reports.index') }}" class="tb-back"><i class="bi bi-arrow-left"></i> Reports</a>
        <h1><i class="bi bi-palette2" style="color:#60a5fa;margin-right:6px"></i>Report Designer</h1>
        <span class="rname">{{ $report->name }}</span>
        <div style="display:flex;gap:8px;margin-left:auto;align-items:center">
            <span id="topStatus" style="font-size:.68rem;color:#94a3b8"></span>
            <button class="btn-tb prev" onclick="togglePreview()"><i class="bi bi-eye"></i> <span id="pvTxt">Preview</span></button>
            <button class="btn-tb save" id="saveBtn" onclick="saveDesign()"><i class="bi bi-cloud-check"></i> Save Design</button>
        </div>
    </div>

    <!-- BODY -->
    <div id="body">

        <!-- ═══ SIDEBAR ═══ -->
        <div id="sb">
            <div id="sbTabs">
                <div class="st on" onclick="sbTab('layout')">Layout</div>
                <div class="st" onclick="sbTab('header')">Header</div>
                <div class="st" onclick="sbTab('columns')">Columns</div>
                <div class="st" onclick="sbTab('style')">Style</div>
                <div class="st" onclick="sbTab('footer')">Footer</div>
            </div>
            <div id="sbBody">

                <!-- ═══ LAYOUT TAB ═══ -->
                <div id="tp-layout">
                    <div class="ss">
                        <div class="sl">Choose layout & configure columns</div>
                        <div class="lc-grid" id="lcGrid"></div>
                        <div style="font-size:.65rem;color:#64748b;padding:6px;background:#f8fafc;border-radius:5px">
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            Click a layout to open the <strong>Column Wizard</strong>. Select which SQL columns to include and configure the table structure.
                        </div>
                    </div>
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Extra dataset (optional)
                            <button class="sbtn pg" style="font-size:.6rem" onclick="addDataset()">+ Add</button>
                        </div>
                        <div id="dsList"></div>
                        <div style="font-size:.63rem;color:#94a3b8;margin-top:4px">Add a second SQL query for a second table (e.g. Bad Customers block)</div>
                    </div>
                </div>

                <!-- ═══ HEADER TAB ═══ -->
                <div id="tp-header" style="display:none">
                    <div class="ss">
                        <div class="sl">Logo</div>
                        <div class="logo-drop" id="ldrop" onclick="document.getElementById('lfi').click()"
                             ondragover="e=>{e.preventDefault();this.classList.add('dg')}"
                             ondragleave="this.classList.remove('dg')"
                             ondrop="onLDrop(event)">
                            <div id="ldropInner">
                                @if(!empty($co['company_logo_url']))
                                    <img src="{{ $co['company_logo_url'] }}">
                                @else
                                    <div style="font-size:.68rem;color:#94a3b8;line-height:1.6"><i class="bi bi-image" style="font-size:1.2rem;display:block;opacity:.4"></i>Drop logo or click</div>
                                @endif
                            </div>
                        </div>
                        <input type="file" id="lfi" accept="image/*" style="display:none" onchange="onLFile(event)">
                        <div class="sr" style="margin-top:5px">
                            <div class="sc"><label>W px</label><input type="number" class="si" id="lw" value="70" min="20" max="200" oninput="updLogo()"></div>
                            <div class="sc"><label>H px</label><input type="number" class="si" id="lh" value="55" min="20" max="120" oninput="updLogo()"></div>
                        </div>
                    </div>
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Add elements</div>
                        <div class="pal">
                            <div class="pb" onclick="addEl('title')"><svg width="22" height="12"><rect x="1" y="1" width="20" height="3" rx="1" fill="currentColor" opacity=".8"/><rect x="3" y="6" width="16" height="2" rx="1" fill="currentColor" opacity=".4"/></svg>Title</div>
                            <div class="pb" onclick="addEl('subtitle')"><svg width="22" height="12"><rect x="3" y="2" width="16" height="2" rx="1" fill="currentColor" opacity=".6"/><rect x="5" y="6" width="12" height="2" rx="1" fill="currentColor" opacity=".3"/></svg>Subtitle</div>
                            <div class="pb" onclick="addEl('logo')"><svg width="22" height="12"><rect x="1" y="1" width="9" height="10" rx="2" fill="currentColor" opacity=".4"/></svg>Logo</div>
                            <div class="pb" onclick="addEl('banner')"><svg width="22" height="12"><rect x="1" y="2" width="20" height="8" rx="1" fill="none" stroke="currentColor" stroke-width="1.2"/><rect x="4" y="5" width="14" height="2" rx="1" fill="currentColor" opacity=".5"/></svg>Banner</div>
                            <div class="pb" onclick="addEl('datebox')"><svg width="22" height="12"><rect x="3" y="1" width="16" height="10" rx="1" fill="none" stroke="currentColor" stroke-width="1.2"/><rect x="6" y="5" width="10" height="2" rx="1" fill="currentColor" opacity=".6"/></svg>Date box</div>
                            <div class="pb" onclick="addEl('company')"><svg width="22" height="12"><rect x="1" y="1" width="11" height="2" rx="1" fill="currentColor" opacity=".8"/><rect x="1" y="5" width="8" height="1.5" rx=".75" fill="currentColor" opacity=".4"/><rect x="1" y="8" width="6" height="1.5" rx=".75" fill="currentColor" opacity=".3"/></svg>Company</div>
                            <div class="pb" onclick="addEl('pagebox')"><svg width="22" height="12"><rect x="13" y="2" width="8" height="8" rx="1" fill="none" stroke="currentColor" stroke-width="1.2"/><rect x="14" y="5" width="6" height="2" rx="1" fill="currentColor" opacity=".5"/></svg>Page no.</div>
                            <div class="pb" onclick="addEl('divider')"><svg width="22" height="12"><line x1="1" y1="6" x2="21" y2="6" stroke="currentColor" stroke-width="1.5"/></svg>Divider</div>
                            <div class="pb" onclick="addEl('text')"><svg width="22" height="12"><rect x="1" y="3" width="20" height="2" rx="1" fill="currentColor" opacity=".5"/><rect x="1" y="7" width="13" height="2" rx="1" fill="currentColor" opacity=".3"/></svg>Text</div>
                        </div>
                    </div>
                    <div class="ss">
                        <div class="sl">Canvas elements</div>
                        <div id="elList"></div>
                    </div>
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Header height <span id="chv" style="font-weight:400;color:#94a3b8">90pt</span></div>
                        <input type="range" min="40" max="200" value="90" style="width:100%" id="chr"
                               oninput="d.cfg.header.height=+this.value;document.getElementById('chv').textContent=this.value+'pt';document.getElementById('zh').style.minHeight=this.value+'px'">
                    </div>

                    <!-- Inside tp-header, after header height slider -->
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Parameters Bar
                            <button class="sbtn pg" style="font-size:.6rem" onclick="addParamField()"><i class="bi bi-plus"></i> Add Field</button>
                        </div>
                        <div style="background:#f8fafc;padding:8px;border-radius:5px;margin-bottom:5px">
                            <div class="stog" style="border-bottom:none;padding:5px 0">
                                <label>Show parameters bar</label>
                                <input type="checkbox" id="showParamsBar" checked onchange="toggleParamsBar(this.checked)">
                            </div>
                        </div>
                        <div id="paramsList" style="margin-top:5px"></div>
                        <div style="font-size:.62rem;color:#94a3b8;margin-top:4px">
                            <i class="bi bi-info-circle"></i> Parameters will appear between header and data table
                        </div>
                    </div>

                </div>

                <!-- ═══ COLUMNS TAB ═══ -->
                <div id="tp-columns" style="display:none">
                    <div class="ss">
                        <div class="sl">Selected columns</div>
                        <div id="colList" style="font-size:.7rem;color:#94a3b8;padding:6px">No columns yet. Choose a layout to run the wizard.</div>
                    </div>
                    <div class="sdiv"></div>
                    <div id="colPropPanel" style="display:none">
                        <div class="col-sel-info">
                            <div style="font-size:.68rem;font-weight:600;color:#1e3a5c;margin-bottom:2px"><i class="bi bi-table me-1"></i>Editing column: <span id="colPropName">—</span></div>
                            <div style="font-size:.62rem;color:#64748b">Click any column header on the canvas to select it</div>
                        </div>
                        <div class="col-prop-grid">
                            <div class="sc"><label>Label</label><input type="text" class="si" id="cp-label" oninput="updSelCol('label',this.value)"></div>
                            <div class="sc"><label>Width (pt)</label><input type="number" class="si" id="cp-width" min="20" max="300" oninput="updSelCol('width',+this.value)"></div>
                            <div class="sc"><label>Align</label>
                                <select class="ssel" id="cp-align" onchange="updSelCol('align',this.value)">
                                    <option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>
                                </select></div>
                            <div class="sc"><label>Font size pt</label><input type="number" class="si" id="cp-fs" min="5" max="14" value="7" oninput="updSelCol('fontSize',+this.value)"></div>
                            <div class="sc"><label>Header bg</label><input type="color" class="sclr" style="width:100%;height:26px" id="cp-thbg" oninput="updSelCol('thBg',this.value)"></div>
                            <div class="sc"><label>Header text</label><input type="color" class="sclr" style="width:100%;height:26px" id="cp-thtext" oninput="updSelCol('thText',this.value)"></div>
                            <div class="sc"><label>Cell bg</label><input type="color" class="sclr" style="width:100%;height:26px" id="cp-tdbg" oninput="updSelCol('tdBg',this.value)"></div>
                            <div class="sc"><label>Cell text</label><input type="color" class="sclr" style="width:100%;height:26px" id="cp-tdtext" oninput="updSelCol('tdText',this.value)"></div>
                            <div class="sc"><label>Alt row bg</label><input type="color" class="sclr" style="width:100%;height:26px" id="cp-altbg" oninput="updSelCol('altBg',this.value)"></div>
                            <div class="sc"><label>Font weight</label>
                                <select class="ssel" id="cp-fw" onchange="updSelCol('fontWeight',this.value)">
                                    <option value="normal">Normal</option><option value="bold">Bold</option>
                                </select></div>
                        </div>
                        <div class="sr" style="margin-top:6px">
                            <div class="sc">
                                <label><input type="checkbox" id="cp-total" onchange="updSelCol('showTotal',this.checked)" style="accent-color:#2563eb;margin-right:4px"> Include in total row</label>
                            </div>
                            <div class="sc">
                                <label><input type="checkbox" id="cp-vis" checked onchange="updSelCol('visible',this.checked)" style="accent-color:#2563eb;margin-right:4px"> Visible</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══ STYLE TAB ═══ -->
                <div id="tp-style" style="display:none">
                    <div class="ss">
                        <div class="sl">Quick presets</div>
                        <div class="cpr" id="cpBtns"></div>
                    </div>
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Global table colours</div>
                        <div id="cgrid"></div>
                    </div>
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Row options</div>
                        <div id="stoggles"></div>
                    </div>
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Group/Salesman row</div>
                        <div class="sr">
                            <div class="sc"><label>Background</label><input type="color" class="sclr" style="width:100%;height:26px" id="g-smbg" oninput="d.cfg.style.sm_bg=this.value;renderData()"></div>
                            <div class="sc"><label>Text color</label><input type="color" class="sclr" style="width:100%;height:26px" id="g-smtext" oninput="d.cfg.style.sm_text=this.value;renderData()"></div>
                        </div>
                    </div>
                </div>

                <!-- ═══ FOOTER TAB ═══ -->
                <div id="tp-footer" style="display:none">
                    <div class="ss">
                        <div class="sl">Footer zones</div>
                        @foreach(['left'=>'Left','center'=>'Center','right'=>'Right'] as $z=>$zl)
                            <div style="margin-bottom:7px">
                                <label style="font-size:.62rem;color:#64748b;display:block;margin-bottom:2px">{{ $zl }}</label>
                                <select class="ssel" id="fz_{{ $z }}" onchange="d.cfg.footer['{{ $z }}']=this.value;renderFooter()">
                                    <option value="company" {{ $z=='left'?'selected':'' }}>Company name</option>
                                    <option value="report_name" {{ $z=='center'?'selected':'' }}>Report name</option>
                                    <option value="datetime">Date &amp; time</option>
                                    <option value="page" {{ $z=='right'?'selected':'' }}>Page number</option>
                                    <option value="custom">Custom text</option>
                                    <option value="blank">Blank</option>
                                </select>
                            </div>
                        @endforeach
                        <div class="sdiv"></div>
                        @foreach(['left'=>'Left','center'=>'Center','right'=>'Right'] as $z=>$zl)
                            <div style="margin-bottom:5px">
                                <label style="font-size:.62rem;color:#94a3b8;display:block;margin-bottom:2px">Custom {{ $zl }}</label>
                                <input type="text" class="si" id="fzc_{{ $z }}" placeholder="text when zone=Custom" oninput="d.cfg.footer.custom_{{ $z }}=this.value">
                            </div>
                        @endforeach
                        <div class="sdiv"></div>
                        <div class="stog"><label>Divider line</label><input type="checkbox" checked onchange="d.cfg.footer.show_divider=this.checked;renderFooter()"></div>
                        <div class="stog"><label>Confidential notice</label><input type="checkbox" id="confChk" checked onchange="d.cfg.style.show_confidential=this.checked;renderData()"></div>
                    </div>
                    <div class="sdiv"></div>
                    <div class="ss">
                        <div class="sl">Page</div>
                        <div class="sr">
                            <div class="sc"><label>Paper</label>
                                <select class="ssel" id="pgPaper" onchange="d.cfg.page.paper_size=this.value">
                                    <option>A4</option><option>A3</option><option>Letter</option><option>Legal</option>
                                </select></div>
                            <div class="sc"><label>Orientation</label>
                                <select class="ssel" id="pgOrient" onchange="d.cfg.page.orientation=this.value">
                                    <option value="landscape">Landscape</option><option value="portrait">Portrait</option>
                                </select></div>
                        </div>
                        <div class="sr">
                            <div class="sc"><label>Font size</label>
                                <select class="ssel" onchange="d.cfg.page.font_size=+this.value;renderData()">
                                    <option value="7">Small 7pt</option><option value="8" selected>Medium 8pt</option><option value="9">Large 9pt</option>
                                </select></div>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- end sidebar -->

        <!-- ═══ CANVAS ═══ -->
        <div id="cw">
            <div id="pw">
                <div id="paper" style="width:930px">
                    <div id="zh" style="min-height:90px;position:relative;overflow:hidden" class="editing">
                        <span class="zlbl">HEADER AREA — add elements from Header tab</span>
                    </div>
                    <div id="zp" style="display:none">Parameters: &nbsp; Date From: 01/01/2025 &nbsp;·&nbsp; Date To: 31/01/2025</div>
                    <div id="zd">
                        <div id="blankState">
                            <i class="bi bi-layout-text-window-reverse bs-icon"></i>
                            <div class="bs-title">Canvas is empty</div>
                            <div class="bs-sub">Select a layout type from the <strong>Layout</strong> tab to launch the column wizard and start building your report.</div>
                        </div>
                    </div>
                    <div id="zf" style="display:none">
                        <span id="pfL"></span><span id="pfC"></span><span id="pfR"></span>
                    </div>
                </div>
            </div>
            <div id="zb">
                <div class="zbt" onclick="setZoom(d.zoom-.15)">−</div>
                <span id="zlbl">100%</span>
                <div class="zbt" onclick="setZoom(d.zoom+.15)">+</div>
                <div class="zbt" title="Fit to screen" onclick="autoFit()">⊡</div>
            </div>
        </div>

    </div><!-- body -->
</div><!-- app -->

<div id="toast"></div>

<!-- ══════════════ WIZARD MODAL ══════════════ -->
<div class="wiz-overlay" id="wizModal" style="display:none">
    <div class="wiz-box">
        <div class="wiz-top">
            <i class="bi bi-magic" style="color:#60a5fa;font-size:1.1rem"></i>
            <h2 id="wizTitle">Column Wizard</h2>
            <span class="wt-sub" id="wizSub">Step 1 of 2</span>
        </div>
        <div class="wiz-body" id="wizBody"></div>
        <div class="wiz-foot">
            <button class="sbtn" onclick="wizClose()">Cancel</button>
            <button class="sbtn" id="wizBack" style="display:none" onclick="wizPrev()"><i class="bi bi-arrow-left me-1"></i>Back</button>
            <button class="sbtn pp" id="wizNext" onclick="wizNext()">Next <i class="bi bi-arrow-right ms-1"></i></button>
        </div>
    </div>
</div>

<script>
    // ═══════════════════════════════════════════════════════════════
    // STATE
    // ═══════════════════════════════════════════════════════════════
    const CO      = @json($co ?? []);
    const EXIST   = @json($report->designer_config ?? null);
    const CSRF    = document.querySelector('meta[name=csrf-token]').content;
    const SAVE_URL  = '{{ route("admin.reports.designer.save", $report) }}';
    const COLS_URL  = '{{ route("admin.reports.columns", $report) }}';

    const DEFS = {
        page:   {paper_size:'A4',orientation:'landscape',font_size:8,margin_top:10,margin_right:8,margin_bottom:16,margin_left:8},
        header: {height:90,elements:[]},
        params: {
            show: true,
            fields: [
                { label: 'Date From', value: ':date_from', default: '01/01/2025', width: 120, align: 'left' },
                { label: 'Date To', value: ':date_to', default: '31/01/2025', width: 120, align: 'left' }
            ]
        },
        layout: null,
        datasets:[{id:1,title:'',show_title:false,date_label:'AS ON',date_value:'',sql_query:'',group_column:'',columns:[]}],
        style:  {th_bg:'#008000',th_text:'#ffffff',sm_bg:'#006600',sm_text:'#ffffff',
            tot_bg:'#004400',tot_text:'#ffffff',even_bg:'#ffffff',odd_bg:'#f0fff5',
            accent:'#2563eb',show_totals:true,indent_customers:true,
            show_confidential:true,show_borders:true,show_row_numbers:false,
            zebra_rows:true,border_color:'#cccccc'},
        footer: {left:'company',center:'report_name',right:'page',
            custom_left:'',custom_center:'',custom_right:'',show_divider:true},
    };

    const d = {
        zoom: 1,
        selEl: null,
        selCol: null,      // index of selected column in datasets[0].columns
        dragging: null, dragOx:0, dragOy:0,
        elCtr: 100,
        dsCtr: 2,
        logoUrl: '',
        preview: false,
        sqlCols: [],       // [{key,label,type}]
        cfg: JSON.parse(JSON.stringify(DEFS)),
        // wizard state
        wiz: {layout:null, step:0, steps:[]}
    };

    // ── Load existing config ──────────────────────────────────────
    if (EXIST) {
        d.cfg = Object.assign(JSON.parse(JSON.stringify(DEFS)), EXIST);
        const le = (d.cfg.header.elements||[]).find(e=>e.type==='logo');
        if (le?.logoUrl && !le.logoUrl.startsWith('[')) {
            d.logoUrl = le.logoUrl;
            document.getElementById('ldropInner').innerHTML = `<img src="${d.logoUrl}" style="max-height:48px;max-width:140px;object-fit:contain">`;
        }
        d.elCtr = Math.max(100,...((d.cfg.header.elements||[]).map(e=>e.id||0)))+1;
        d.dsCtr = Math.max(2,...((d.cfg.datasets||[]).map(x=>x.id||0)))+1;
    }

    // ── Auto-init if existing config has columns ──────────────────
    if (EXIST && d.cfg.datasets[0]?.columns?.length) {
        d.sqlCols = d.cfg.datasets[0].columns.map(c=>({key:c.sql_key,label:c.label,type:c.type}));
    }

    // ═══════════════════════════════════════════════════════════════
    // TABS
    // ═══════════════════════════════════════════════════════════════
    const TABS = ['layout','header','columns','style','footer'];
    function sbTab(t) {
        TABS.forEach((id,i)=>{
            document.getElementById('tp-'+id).style.display=id===t?'block':'none';
            document.querySelectorAll('.st')[i].classList.toggle('on',id===t);
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // ZOOM
    // ═══════════════════════════════════════════════════════════════
    function setZoom(z) {
        d.zoom = Math.max(0.2, Math.min(1.5, parseFloat(z.toFixed(2))));
        const pw = document.getElementById('pw');
        pw.style.transform = `scale(${d.zoom})`;
        pw.style.transformOrigin = 'top center';
        // compensate height so scroll works
        const paper = document.getElementById('paper');
        pw.style.height = (paper.offsetHeight * d.zoom + 48) + 'px';
        document.getElementById('zlbl').textContent = Math.round(d.zoom*100)+'%';
    }
    function autoFit() {
        const cw = document.getElementById('cw').clientWidth - 48;
        const pw = document.getElementById('paper').offsetWidth;
        setZoom(Math.min(1, cw/pw));
    }
    window.addEventListener('resize', ()=>setTimeout(autoFit,100));

    // ═══════════════════════════════════════════════════════════════
    // LOAD SQL COLUMNS FROM SERVER
    // ═══════════════════════════════════════════════════════════════
    async function loadSqlCols() {
        try {
            const r = await fetch(COLS_URL, {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                body: JSON.stringify({company:'0'})
            });
            const data = await r.json();
            if (data.success && data.columns.length) {
                d.sqlCols = data.columns.map(c => ({
                    key:  c,
                    label: c.replace(/_/g,' ').replace(/\b\w/g,l=>l.toUpperCase()),
                    type: /amount|dues|total|days|balance|debit|credit|qty|price|cost|tax|\d{1,2}\s*[-–]\s*\d{1,2}/i.test(c) ? 'number' : 'text'
                }));
                return true;
            }
        } catch(e) {}
        return false;
    }

    // ═══════════════════════════════════════════════════════════════
    // LAYOUT GRID
    // ═══════════════════════════════════════════════════════════════
    const LAYOUTS = [
        {v:'tabular',      label:'Tabular',        icon:'bi-table',                desc:'Flat rows + totals'},
        {v:'grouped',      label:'Grouped',         icon:'bi-collection',           desc:'Rows grouped by salesman'},
        {v:'master-detail',label:'Master-Detail',  icon:'bi-layout-text-sidebar',  desc:'Document header + lines'},
        {v:'statement',    label:'Statement',       icon:'bi-file-earmark-ruled',   desc:'Account with balance'},
    ];
    function buildLayoutGrid() {
        const g = document.getElementById('lcGrid'); g.innerHTML='';
        LAYOUTS.forEach(l=>{
            const c = document.createElement('div');
            const on = d.cfg.layout===l.v;
            c.className = 'lc'+(on?' on':'');
            c.innerHTML = `<i class="bi ${l.icon}"></i><div class="lcl">${l.label}</div><div style="font-size:.55rem;color:#94a3b8;margin-top:1px">${l.desc}</div>`;
            c.onclick = () => openWizard(l.v);
            g.appendChild(c);
        });
    }

    // ═══════════════════════════════════════════════════════════════
    // PARAMETERS BAR CONFIGURATION
    // ═══════════════════════════════════════════════════════════════
    function renderParamsList() {
        const container = document.getElementById('paramsList');
        const params = d.cfg.params || { show: true, fields: [] };

        if (!params.fields || params.fields.length === 0) {
            container.innerHTML = '<div style="font-size:.68rem;color:#94a3b8;text-align:center;padding:12px;border:1px dashed #e2e8f0;border-radius:5px"><i class="bi bi-info-circle me-1"></i>No parameter fields. Click "Add Field" to create one.</div>';
            return;
        }

        container.innerHTML = params.fields.map((field, index) => `
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:5px;padding:8px;margin-bottom:6px;position:relative">
            <div style="display:flex;gap:6px;margin-bottom:5px">
                <div style="flex:2">
                    <label style="font-size:.6rem;color:#64748b">Label</label>
                    <input type="text" class="si" value="${field.label}"
                           onchange="updateParamField(${index}, 'label', this.value)"
                           placeholder="e.g. Date From">
                </div>
                <div style="flex:1">
                    <label style="font-size:.6rem;color:#64748b">Width (pt)</label>
                    <input type="number" class="si" value="${field.width || 100}" min="60" max="200"
                           onchange="updateParamField(${index}, 'width', +this.value)">
                </div>
            </div>
            <div style="display:flex;gap:6px;margin-bottom:5px">
                <div style="flex:2">
                    <label style="font-size:.6rem;color:#64748b">Parameter / Value</label>
                    <input type="text" class="si" value="${field.value}"
                           onchange="updateParamField(${index}, 'value', this.value)"
                           placeholder=":param_name or static text">
                </div>
                <div style="flex:1">
                    <label style="font-size:.6rem;color:#64748b">Default</label>
                    <input type="text" class="si" value="${field.default || ''}"
                           onchange="updateParamField(${index}, 'default', this.value)"
                           placeholder="preview value">
                </div>
            </div>
            <div style="display:flex;gap:6px;align-items:center">
                <div style="flex:1">
                    <label style="font-size:.6rem;color:#64748b">Alignment</label>
                    <select class="ssel" onchange="updateParamField(${index}, 'align', this.value)">
                        <option value="left" ${field.align === 'left' ? 'selected' : ''}>Left</option>
                        <option value="center" ${field.align === 'center' ? 'selected' : ''}>Center</option>
                        <option value="right" ${field.align === 'right' ? 'selected' : ''}>Right</option>
                    </select>
                </div>
                <div style="flex:0 0 auto;margin-top:12px">
                    <button class="sbtn pd" style="font-size:.6rem" onclick="removeParamField(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    }

    function addParamField() {
        if (!d.cfg.params) d.cfg.params = { show: true, fields: [] };
        d.cfg.params.fields.push({
            label: 'New Parameter',
            value: ':param',
            default: 'Value',
            width: 120,
            align: 'left'
        });
        renderParamsList();
        renderParamsBar();
    }

    function updateParamField(index, key, value) {
        if (d.cfg.params && d.cfg.params.fields[index]) {
            d.cfg.params.fields[index][key] = value;
            renderParamsBar();
        }
    }

    function removeParamField(index) {
        if (d.cfg.params && d.cfg.params.fields) {
            d.cfg.params.fields.splice(index, 1);
            renderParamsList();
            renderParamsBar();
        }
    }

    function toggleParamsBar(show) {
        if (!d.cfg.params) d.cfg.params = { show: true, fields: [] };
        d.cfg.params.show = show;
        renderParamsBar();
    }

    function renderParamsBar() {
        const paramsZone = document.getElementById('zp');
        const params = d.cfg.params || { show: true, fields: [] };

        if (!params.show || !params.fields || params.fields.length === 0) {
            paramsZone.style.display = 'none';
            return;
        }

        paramsZone.style.display = 'block';

        let html = '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;justify-content:flex-start;padding:4pt 10pt;background:#f8faff;border-bottom:1px solid #e8edf5;font-size:7pt">';

        params.fields.forEach(field => {
            const width = field.width || 100;
            const align = field.align || 'left';
            const value = field.default || field.value;

            html += `
            <div style="display:flex;align-items:center;gap:4px;min-width:${width}pt">
                <span style="font-weight:600;color:#4a5b6e">${field.label}:</span>
                <span style="color:#1e3a5c;background:#fff;padding:2px 6px;border:1px solid #dde3ec;border-radius:3px;text-align:${align};flex:1">${value}</span>
            </div>
        `;
        });

        html += '</div>';
        paramsZone.innerHTML = html;
    }

    // Update the renderData function to include params bar
    // Add this line at the beginning of the existing renderData function:
    function renderData() {
        // First render params bar
        renderParamsBar();

        // Rest of existing renderData code...
        const zone   = document.getElementById('zd');
        const cols   = d.cfg.datasets[0].columns || [];
        const layout = d.cfg.layout;
        const st     = d.cfg.style;

        if (!layout || !cols.length) {
            zone.innerHTML = `<div id="blankState"><i class="bi bi-layout-text-window-reverse bs-icon"></i>
        <div class="bs-title">Canvas is empty</div>
        <div class="bs-sub">Select a layout from the <strong>Layout</strong> tab to start building.</div></div>`;
            return;
        }

        // ... rest of existing renderData code continues ...
    }

    // ═══════════════════════════════════════════════════════════════
    // ═══ WIZARD ═══════════════════════════════════════════════════
    // ═══════════════════════════════════════════════════════════════
    let wiz = {layout:null, step:0, maxStep:1, selections:{}};

    async function openWizard(layout) {
        // Load columns if not yet loaded
        if (!d.sqlCols.length) {
            showToast('Loading SQL columns…');
            const ok = await loadSqlCols();
            if (!ok || !d.sqlCols.length) {
                showToast('Could not load columns. Check SQL query is saved.', true);
                return;
            }
        }
        wiz = {layout, step:0, maxStep:0, selections:{}};
        // Set max steps by layout
        if (layout === 'master-detail') wiz.maxStep = 2;
        else if (layout === 'grouped')  wiz.maxStep = 1;
        else                             wiz.maxStep = 0;

        document.getElementById('wizModal').style.display = 'flex';
        renderWizStep();
    }
    function wizClose() {
        document.getElementById('wizModal').style.display = 'none';
    }
    function wizNext() {
        if (!collectWizStep()) return;
        if (wiz.step < wiz.maxStep) {
            wiz.step++;
            renderWizStep();
        } else {
            applyWizard();
            wizClose();
        }
    }
    function wizPrev() {
        if (wiz.step > 0) { wiz.step--; renderWizStep(); }
    }

    function collectWizStep() {
        const layout = wiz.layout;
        if (layout === 'master-detail') {
            if (wiz.step === 0) {
                wiz.selections.masterCols = [...document.querySelectorAll('#wizBody input[name="master"]:checked')].map(e=>e.value);
                if (!wiz.selections.masterCols.length) { showToast('Select at least one master column', true); return false; }
            } else if (wiz.step === 1) {
                wiz.selections.detailCols = [...document.querySelectorAll('#wizBody input[name="detail"]:checked')].map(e=>e.value);
                if (!wiz.selections.detailCols.length) { showToast('Select at least one detail column', true); return false; }
                wiz.selections.totalCols = [...document.querySelectorAll('#wizBody input[name="total"]:checked')].map(e=>e.value);
            } else if (wiz.step === 2) {
                wiz.selections.totalCols = [...document.querySelectorAll('#wizBody input[name="totcol"]:checked')].map(e=>e.value);
            }
        } else if (layout === 'grouped') {
            if (wiz.step === 0) {
                wiz.selections.groupCol = document.querySelector('#wizBody input[name="gcol"]:checked')?.value||'';
                if (!wiz.selections.groupCol) { showToast('Select a group column', true); return false; }
            } else {
                wiz.selections.dataCols = [...document.querySelectorAll('#wizBody input[name="dcol"]:checked')].map(e=>e.value);
                wiz.selections.totalCols = [...document.querySelectorAll('#wizBody input[name="totcol"]:checked')].map(e=>e.value);
                if (!wiz.selections.dataCols.length) { showToast('Select at least one data column', true); return false; }
            }
        } else {
            // tabular / statement
            wiz.selections.cols = [...document.querySelectorAll('#wizBody input[name="col"]:checked')].map(e=>e.value);
            wiz.selections.totalCols = [...document.querySelectorAll('#wizBody input[name="totcol"]:checked')].map(e=>e.value);
            if (!wiz.selections.cols.length) { showToast('Select at least one column', true); return false; }
        }
        return true;
    }

    function renderWizStep() {
        const layout  = wiz.layout;
        const step    = wiz.step;
        const maxStep = wiz.maxStep;
        const body    = document.getElementById('wizBody');
        const nextBtn = document.getElementById('wizNext');
        const backBtn = document.getElementById('wizBack');

        document.getElementById('wizTitle').textContent = LAYOUTS.find(l=>l.v===layout)?.label + ' — Column Wizard';
        document.getElementById('wizSub').textContent = `Step ${step+1} of ${maxStep+1}`;
        backBtn.style.display = step>0?'':'none';
        nextBtn.innerHTML = step<maxStep ? 'Next <i class="bi bi-arrow-right ms-1"></i>' : '<i class="bi bi-check-circle me-1"></i> Apply';

        let html = '';

        if (layout === 'tabular' || layout === 'statement') {
            html += `<div class="wiz-step-title">Select columns to display</div>
            <div class="wiz-step-sub">Choose which SQL columns appear in the report table. Numeric columns can show totals.</div>
            <div class="col-grid">`;
            d.sqlCols.forEach(c=>{
                const sel = wiz.selections.cols?.includes(c.key);
                html+=`<label class="col-cb ${sel?'checked':''}" onclick="this.classList.toggle('checked',this.querySelector('input').checked)">
                <input type="checkbox" name="col" value="${c.key}" ${sel?'checked':''}> ${c.label}
                <span class="ct ${c.type==='number'?'n':'t'}">${c.type==='number'?'#':'T'}</span>
            </label>`;
            });
            html+=`</div>`;
            // Total columns
            const numCols = d.sqlCols.filter(c=>c.type==='number');
            if (numCols.length) {
                html+=`<div class="wiz-step-title" style="margin-top:12px">Show totals for</div>
                <div class="wiz-step-sub">Tick numeric columns that should have a grand total row.</div>`;
                numCols.forEach(c=>{
                    const sel = wiz.selections.totalCols?.includes(c.key);
                    html+=`<div class="tot-row"><input type="checkbox" name="totcol" value="${c.key}" ${sel?'checked':''}>
                    <span class="lbl">${c.label}</span><span class="tc">#</span></div>`;
                });
            }

        } else if (layout === 'grouped') {
            if (step === 0) {
                html+=`<div class="wiz-step-title">Select the group column (salesman / category)</div>
                <div class="wiz-step-sub">This column creates bold header rows that group customers together. Typically the salesman name.</div>
                <div class="col-grid">`;
                d.sqlCols.filter(c=>c.type==='text').forEach(c=>{
                    const sel = wiz.selections.groupCol===c.key;
                    html+=`<label class="col-cb ${sel?'checked':''}">
                    <input type="radio" name="gcol" value="${c.key}" ${sel?'checked':''}> ${c.label}
                    <span class="ct t">T</span>
                </label>`;
                });
                html+=`</div>`;
            } else {
                html+=`<div class="wiz-step-title">Select data columns</div>
                <div class="wiz-step-sub">Choose columns to show per row (customer name, amounts, aging buckets etc.)</div>
                <div class="col-grid">`;
                d.sqlCols.filter(c=>c.key!==wiz.selections.groupCol).forEach(c=>{
                    const sel = wiz.selections.dataCols?.includes(c.key)!==false;
                    html+=`<label class="col-cb ${sel?'checked':''}">
                    <input type="checkbox" name="dcol" value="${c.key}" ${sel?'checked':''}> ${c.label}
                    <span class="ct ${c.type==='number'?'n':'t'}">${c.type==='number'?'#':'T'}</span>
                </label>`;
                });
                html+=`</div>`;
                const numCols = d.sqlCols.filter(c=>c.type==='number'&&c.key!==wiz.selections.groupCol);
                if (numCols.length) {
                    html+=`<div class="wiz-step-title" style="margin-top:12px">Grand total columns</div>`;
                    numCols.forEach(c=>{
                        const sel = wiz.selections.totalCols?.includes(c.key);
                        html+=`<div class="tot-row"><input type="checkbox" name="totcol" value="${c.key}" ${sel?'checked':''}>
                        <span class="lbl">${c.label}</span><span class="tc">#</span></div>`;
                    });
                }
            }

        } else if (layout === 'master-detail') {
            if (step === 0) {
                html+=`<div class="wiz-step-title">Select master (header) columns</div>
                <div class="wiz-step-sub">These fields appear in the document header block — e.g. Invoice No, Date, Customer Name, Address.</div>
                <div class="col-grid">`;
                d.sqlCols.forEach(c=>{
                    const sel = wiz.selections.masterCols?.includes(c.key);
                    html+=`<label class="col-cb ${sel?'checked':''}">
                    <input type="checkbox" name="master" value="${c.key}" ${sel?'checked':''}> ${c.label}
                    <span class="ct ${c.type==='number'?'n':'t'}">${c.type==='number'?'#':'T'}</span>
                </label>`;
                });
                html+=`</div>`;
            } else if (step === 1) {
                html+=`<div class="wiz-step-title">Select detail (line item) columns</div>
                <div class="wiz-step-sub">These appear in the line items table — e.g. Item, Qty, Rate, Amount.</div>
                <div class="col-grid">`;
                d.sqlCols.filter(c=>!wiz.selections.masterCols?.includes(c.key)).forEach(c=>{
                    const sel = wiz.selections.detailCols?.includes(c.key)!==false;
                    html+=`<label class="col-cb ${sel?'checked':''}">
                    <input type="checkbox" name="detail" value="${c.key}" ${sel?'checked':''}> ${c.label}
                    <span class="ct ${c.type==='number'?'n':'t'}">${c.type==='number'?'#':'T'}</span>
                </label>`;
                });
                html+=`</div>`;
                const numDC = d.sqlCols.filter(c=>c.type==='number'&&!wiz.selections.masterCols?.includes(c.key));
                if (numDC.length) {
                    html+=`<div class="wiz-step-title" style="margin-top:12px">Total columns</div>`;
                    numDC.forEach(c=>{
                        const sel = wiz.selections.totalCols?.includes(c.key);
                        html+=`<div class="tot-row"><input type="checkbox" name="total" value="${c.key}" ${sel?'checked':''}>
                        <span class="lbl">${c.label}</span><span class="tc">#</span></div>`;
                    });
                }
            }
        }

        body.innerHTML = html;
        // Make clicking label toggle check state
        body.querySelectorAll('.col-cb').forEach(lbl=>{
            lbl.addEventListener('change', ()=>{
                lbl.classList.toggle('checked', lbl.querySelector('input').checked);
            });
        });
    }

    function applyWizard() {
        const layout = wiz.layout;
        const sel    = wiz.selections;
        const st     = d.cfg.style;
        let cols = [];

        const makeCol = (key, extra={}) => {
            const meta = d.sqlCols.find(c=>c.key===key)||{key,label:key,type:'text'};
            return Object.assign({
                sql_key: key,
                label:   meta.label,
                type:    meta.type,
                width:   meta.type==='number'?65:180,
                align:   meta.type==='number'?'right':'left',
                fontSize: 7,
                thBg:    st.th_bg,
                thText:  st.th_text,
                tdBg:    '',  // transparent = use row bg
                tdText:  '',
                altBg:   '',
                fontWeight: 'normal',
                showTotal: meta.type==='number',
                visible: true,
                zone: 'data',   // data | master | detail
            }, extra);
        };

        if (layout === 'tabular' || layout === 'statement') {
            cols = sel.cols.map(k => makeCol(k, {showTotal: sel.totalCols?.includes(k)}));

        } else if (layout === 'grouped') {
            // group col first (not shown per-row), then data cols
            cols = [
                makeCol(sel.groupCol, {zone:'group', width:200, fontWeight:'bold', showTotal:false, visible:true}),
                ...sel.dataCols.map(k => makeCol(k, {showTotal: sel.totalCols?.includes(k)}))
            ];

        } else if (layout === 'master-detail') {
            cols = [
                ...sel.masterCols.map(k => makeCol(k, {zone:'master', width:120})),
                ...sel.detailCols.map(k => makeCol(k, {zone:'detail', showTotal: sel.totalCols?.includes(k)}))
            ];
        }

        d.cfg.layout = layout;
        d.cfg.datasets[0].columns = cols;
        d.cfg.datasets[0].group_column = sel.groupCol||'';

        // Update all sidebar selects, canvas
        buildLayoutGrid();
        renderData();
        renderColList();
        sbTab('columns');

        // Show footer + params bar now that we have data
        document.getElementById('zp').style.display = '';
        document.getElementById('zf').style.display = '';
        renderFooter();
    }

    // ═══════════════════════════════════════════════════════════════
    // RENDER DATA ZONE
    // ═══════════════════════════════════════════════════════════════
    function renderData() {
        const zone   = document.getElementById('zd');
        const cols   = d.cfg.datasets[0].columns || [];
        const layout = d.cfg.layout;
        const st     = d.cfg.style;

        if (!layout || !cols.length) {
            zone.innerHTML = `<div id="blankState"><i class="bi bi-layout-text-window-reverse bs-icon"></i>
            <div class="bs-title">Canvas is empty</div>
            <div class="bs-sub">Select a layout from the <strong>Layout</strong> tab to start building.</div></div>`;
            return;
        }

        const BD = st.show_borders ? `border:0.5pt solid ${st.border_color}` : `border-bottom:0.5pt solid ${st.border_color}`;
        const fsPt = (d.cfg.page.font_size||8)+'pt';

        let html = '';

        // Dataset title rows
        d.cfg.datasets.forEach(ds=>{
            if (ds.show_title && ds.title) {
                html+=`<div style="font-size:9.5pt;font-weight:bold;text-align:center;text-decoration:underline;text-transform:uppercase;padding:6pt 0 2pt">${ds.title}`;
                if (ds.date_label) html+=` <span style="border:0.5pt solid #000;padding:2pt 8pt;font-size:7.5pt;margin-left:8pt">${ds.date_label}&nbsp;&nbsp;${ds.date_value||'[date]'}</span>`;
                html+=`</div>`;
            }
        });

        if (layout === 'master-detail') {
            const mCols = cols.filter(c=>c.zone==='master');
            const dCols = cols.filter(c=>c.zone==='detail');

            // Master block (header info)
            html += `<div style="border:1pt solid #e2e8f0;border-top:3pt solid ${st.th_bg};padding:7pt 10pt;margin-bottom:4pt;background:#fafafa">
            <table style="width:100%;border:none;font-size:${fsPt}">
            <tr>${mCols.map(c=>`<td style="padding:2pt 8pt 2pt 0;vertical-align:top;width:${c.width||120}pt;${c.tdBg?'background:'+c.tdBg+';':''}${c.tdText?'color:'+c.tdText+';':''}">
                <span style="font-size:6pt;text-transform:uppercase;color:#8096ac;font-weight:bold;display:block">${c.label}</span>
                <span style="font-size:8pt;font-weight:bold;color:${st.th_bg}">Sample ${c.label}</span>
            </td>`).join('')}
            </tr></table>
        </div>`;

            html += buildTableHTML(dCols, null, null, st, BD, fsPt);
        } else if (layout === 'grouped') {
            const gCol  = cols.find(c=>c.zone==='group');
            const dCols = cols.filter(c=>c.zone!=='group');
            html += buildGroupedHTML(gCol, dCols, st, BD, fsPt);
        } else {
            // tabular / statement
            html += buildTableHTML(cols, null, null, st, BD, fsPt);
        }

        if (st.show_confidential)
            html += `<div style="font-size:6pt;color:#888;text-align:center;margin-top:5pt;border-top:0.5pt solid #ddd;padding-top:3pt">Company Confidential - Internal Distribution ONLY &nbsp;·&nbsp; Computer Generated Document</div>`;

        zone.innerHTML = html;

        // Attach click handlers to column headers
        zone.querySelectorAll('th[data-ci]').forEach(th=>{
            th.addEventListener('click', e=>{ e.stopPropagation(); selectCol(+th.dataset.ci, +th.dataset.di||0); });
        });
    }

    function buildTableHTML(cols, masterCols, totalCols, st, BD, fsPt) {
        const visibleCols = cols.filter(c=>c.visible!==false);
        let html = `<table class="dt" style="font-size:${fsPt}"><thead><tr>`;
        if (d.cfg.style.show_row_numbers)
            html += `<th style="width:18pt;background:${st.th_bg};color:${st.th_text};padding:3pt 4pt;${BD};text-align:center">#</th>`;
        visibleCols.forEach((col,ci)=>{
            const realCi = (d.cfg.datasets[0].columns||[]).indexOf(col);
            const isSelCol = d.selCol===realCi;
            const thBg  = col.thBg  || st.th_bg;
            const thTxt = col.thText|| st.th_text;
            html+=`<th data-ci="${realCi}" data-di="0" class="${isSelCol?'col-sel':''}"
            style="background:${thBg};color:${thTxt};padding:3pt 5pt;${BD};width:${col.width||65}pt;
            text-align:${col.align||'center'};font-size:${col.fontSize||7}pt;cursor:pointer"
            title="Click to edit column style">${col.label}
            <div class="col-resize-handle"></div></th>`;
        });
        html += `</tr></thead><tbody>`;

        // Sample rows
        const sampleVals = {text:['Customer Alpha Traders','Beta Enterprises','Gamma & Sons Ltd'],number:['2,80,000','1,50,000','95,000']};
        [0,1,2].forEach((ri)=>{
            const bg  = d.cfg.style.zebra_rows?(ri%2===0?st.even_bg:st.odd_bg):st.even_bg;
            if (d.cfg.style.show_row_numbers)
                html+=`<tr><td style="background:${bg};${BD};text-align:right;color:#aaa;font-size:6pt;padding:2pt 4pt">${ri+1}</td>`;
            else html+=`<tr>`;
            visibleCols.forEach((col)=>{
                const tdBg  = col.tdBg  || bg;
                const tdTxt = col.tdText|| '';
                const isNum = col.type==='number';
                const v = sampleVals[isNum?'number':'text'][ri]||'';
                html+=`<td style="background:${tdBg};${tdTxt?'color:'+tdTxt+';':''}${BD};padding:2.5pt 5pt;text-align:${col.align||'left'};${isNum?'font-family:monospace;':''}font-weight:${col.fontWeight||'normal'}">${v}</td>`;
            });
            html+=`</tr>`;
        });

        // Totals
        if (d.cfg.style.show_totals) {
            html+=`<tr>`;
            if (d.cfg.style.show_row_numbers) html+=`<td style="background:${st.tot_bg};${BD}"></td>`;
            visibleCols.forEach((col,i)=>{
                const isNum=col.type==='number', showTot=col.showTotal!==false&&isNum;
                html+=`<td style="background:${st.tot_bg};color:${st.tot_text};font-weight:bold;padding:3pt 5pt;text-align:${isNum?'right':'right'};${BD};font-family:${isNum?'monospace':'inherit'}">${i===0?'Total :':showTot?'5,25,000':''}</td>`;
            });
            html+=`</tr>`;
        }
        html += `</tbody></table>`;
        return html;
    }

    function buildGroupedHTML(gCol, dCols, st, BD, fsPt) {
        const vis = dCols.filter(c=>c.visible!==false);
        let html=`<table class="dt" style="font-size:${fsPt}"><thead>`;

        // Check if we have bucket cols (aging-style)
        const numCols   = vis.filter(c=>c.type==='number');
        const textCols  = vis.filter(c=>c.type==='text');
        const nameCol   = textCols[0];
        const totalCol  = numCols[0];
        const buckets   = numCols.slice(1);

        if (buckets.length) {
            html+=`<tr>`;
            if (d.cfg.style.show_row_numbers) html+=`<th rowspan="2" style="width:18pt;background:${st.th_bg};color:${st.th_text};padding:3pt 4pt;${BD}">#</th>`;
            if (nameCol) {
                const realCi=(d.cfg.datasets[0].columns||[]).indexOf(nameCol);
                html+=`<th rowspan="2" data-ci="${realCi}" data-di="0" style="background:${nameCol.thBg||st.th_bg};color:${nameCol.thText||st.th_text};padding:3pt 5pt;${BD};width:${nameCol.width||180}pt;text-align:left;cursor:pointer">${nameCol.label}</th>`;
            }
            if (totalCol) {
                const realCi=(d.cfg.datasets[0].columns||[]).indexOf(totalCol);
                html+=`<th rowspan="2" data-ci="${realCi}" data-di="0" style="background:${totalCol.thBg||st.th_bg};color:${totalCol.thText||st.th_text};padding:3pt 5pt;${BD};width:${totalCol.width||72}pt;cursor:pointer">${totalCol.label}</th>`;
            }
            html+=`<th colspan="${buckets.length}" style="background:${st.th_bg};color:${st.th_text};padding:3pt 5pt;${BD};text-align:center">Dues Days</th></tr><tr>`;
            buckets.forEach(col=>{
                const realCi=(d.cfg.datasets[0].columns||[]).indexOf(col);
                html+=`<th data-ci="${realCi}" data-di="0" style="background:${col.thBg||st.th_bg};color:${col.thText||st.th_text};padding:2pt 4pt;${BD};width:${col.width||62}pt;cursor:pointer">${col.label}</th>`;
            });
            html+=`</tr>`;
        } else {
            html+=`<tr>`;
            if (d.cfg.style.show_row_numbers) html+=`<th style="width:18pt;background:${st.th_bg};color:${st.th_text};padding:3pt 4pt;${BD}">#</th>`;
            vis.forEach(col=>{
                const realCi=(d.cfg.datasets[0].columns||[]).indexOf(col);
                html+=`<th data-ci="${realCi}" data-di="0" style="background:${col.thBg||st.th_bg};color:${col.thText||st.th_text};padding:3pt 5pt;${BD};width:${col.width||65}pt;text-align:${col.align||'center'};cursor:pointer">${col.label}</th>`;
            });
            html+=`</tr>`;
        }
        html+=`</thead><tbody>`;

        // Salesman row
        html+=`<tr><td colspan="${vis.length+(d.cfg.style.show_row_numbers?1:0)}" style="background:${st.sm_bg};color:${st.sm_text};font-weight:bold;padding:2.5pt 5pt;${BD}">SAMPLE SALESMAN / GROUP</td></tr>`;

        // Sample customer rows
        const sampleRows=[['Customer Alpha',  '2,80,000','1,20,000','80,000','60,000','','20,000'],
            ['Customer Beta',   '1,50,000','','1,50,000','','',''],
            ['Customer Gamma',  '95,000',  '','','','','95,000']];
        sampleRows.forEach((row,ri)=>{
            const bg=d.cfg.style.zebra_rows?(ri%2===0?st.even_bg:st.odd_bg):st.even_bg;
            html+=`<tr>`;
            if (d.cfg.style.show_row_numbers) html+=`<td style="background:${bg};${BD};text-align:right;color:#aaa;font-size:6pt;padding:2pt 4pt">${ri+1}</td>`;
            if (nameCol) html+=`<td style="background:${nameCol.tdBg||bg};${BD};padding:2pt ${d.cfg.style.indent_customers?'14':'5'}pt 2pt 5pt">${row[0]}</td>`;
            if (totalCol) html+=`<td style="background:${totalCol.tdBg||bg};${BD};text-align:right;font-family:monospace;padding:2pt 4pt">${row[1]}</td>`;
            buckets.forEach((_,bi)=>html+=`<td style="background:${_.tdBg||bg};${BD};text-align:right;font-family:monospace;padding:2pt 4pt">${row[bi+2]||''}</td>`);
            html+=`</tr>`;
        });

        // Totals
        if (d.cfg.style.show_totals) {
            html+=`<tr>`;
            if (d.cfg.style.show_row_numbers) html+=`<td style="background:${st.tot_bg};${BD}"></td>`;
            if (nameCol) html+=`<td style="background:${st.tot_bg};color:${st.tot_text};font-weight:bold;text-align:right;${BD};padding:3pt 5pt">Total :</td>`;
            if (totalCol) html+=`<td style="background:${st.tot_bg};color:${st.tot_text};font-weight:bold;text-align:right;font-family:monospace;${BD};padding:3pt 4pt">5,25,000</td>`;
            buckets.forEach((_, bi)=>html+=`<td style="background:${st.tot_bg};color:${st.tot_text};font-weight:bold;text-align:right;font-family:monospace;${BD};padding:2pt 4pt">${['1,20,000','2,30,000','60,000','','20,000','95,000'][bi]||''}</td>`);
            html+=`</tr>`;
        }
        html+=`</tbody></table>`;
        return html;
    }

    // ═══════════════════════════════════════════════════════════════
    // COLUMN SELECTION
    // ═══════════════════════════════════════════════════════════════
    function selectCol(ci, di) {
        d.selCol = ci;
        sbTab('columns');
        renderData();
        renderColList();
        renderColPropPanel();
    }
    function renderColList() {
        const c = document.getElementById('colList');
        const cols = d.cfg.datasets[0].columns||[];
        if (!cols.length) { c.innerHTML='<div style="font-size:.7rem;color:#94a3b8;padding:6px">No columns. Run the wizard first.</div>'; return; }
        c.innerHTML = cols.map((col,i)=>`<div style="display:flex;align-items:center;gap:5px;padding:4px 6px;border:1px solid ${d.selCol===i?'#2563eb':'#e2e8f0'};border-radius:4px;margin-bottom:3px;cursor:pointer;background:${d.selCol===i?'#eff6ff':'#fff'};font-size:.7rem" onclick="selectCol(${i},0)">
        <span style="flex:1">${col.label}</span>
        <span style="font-size:.55rem;background:${col.type==='number'?'#d1fae5':'#dbeafe'};color:${col.type==='number'?'#065f46':'#1e40af'};padding:1px 5px;border-radius:10px">${col.type==='number'?'#':'T'}</span>
        <span style="font-size:.55rem;background:#f1f5f9;padding:1px 5px;border-radius:10px;color:#64748b">${col.zone||'data'}</span>
    </div>`).join('');
        document.getElementById('colPropPanel').style.display = d.selCol!==null ? 'block' : 'none';
    }
    function renderColPropPanel() {
        const col = (d.cfg.datasets[0].columns||[])[d.selCol];
        if (!col) { document.getElementById('colPropPanel').style.display='none'; return; }
        document.getElementById('colPropPanel').style.display='block';
        document.getElementById('colPropName').textContent = col.label;
        document.getElementById('cp-label').value    = col.label||'';
        document.getElementById('cp-width').value    = col.width||65;
        document.getElementById('cp-align').value    = col.align||'left';
        document.getElementById('cp-fs').value       = col.fontSize||7;
        document.getElementById('cp-thbg').value     = col.thBg   || d.cfg.style.th_bg;
        document.getElementById('cp-thtext').value   = col.thText || d.cfg.style.th_text;
        document.getElementById('cp-tdbg').value     = col.tdBg   || '#ffffff';
        document.getElementById('cp-tdtext').value   = col.tdText || '#000000';
        document.getElementById('cp-altbg').value    = col.altBg  || d.cfg.style.odd_bg;
        document.getElementById('cp-fw').value       = col.fontWeight||'normal';
        document.getElementById('cp-total').checked  = col.showTotal!==false&&col.type==='number';
        document.getElementById('cp-vis').checked    = col.visible!==false;
    }
    function updSelCol(key,val) {
        const col = (d.cfg.datasets[0].columns||[])[d.selCol];
        if (!col) return;
        col[key]=val;
        if (key==='label') document.getElementById('colPropName').textContent=val;
        renderData();
        renderColList();
    }

    // ═══════════════════════════════════════════════════════════════
    // HEADER ELEMENTS
    // ═══════════════════════════════════════════════════════════════
    const EL_DEF = {
        logo:    {w:70,h:55,text:'',fontSize:8,bold:false,italic:false,color:'#000000',bg:'',borderColor:'',align:'left'},
        title:   {w:750,h:28,text:'{report_name}',fontSize:12,bold:true,italic:false,color:'#000000',bg:'',borderColor:'',align:'center'},
        subtitle:{w:600,h:18,text:'Sub-heading',fontSize:9,bold:false,italic:true,color:'#555555',bg:'',borderColor:'',align:'center'},
        company: {w:240,h:50,text:(CO.company_name||'Company Name')+'\n'+(CO.company_address||''),fontSize:9,bold:true,italic:false,color:'#000000',bg:'',borderColor:'',align:'left'},
        banner:  {w:240,h:20,text:'Computer Generated Document',fontSize:7,bold:true,italic:true,color:'#000000',bg:'#ffffff',borderColor:'#000000',align:'center'},
        datebox: {w:165,h:18,text:'AS ON   {as_on_date}',fontSize:8,bold:true,italic:false,color:'#000000',bg:'#ffffff',borderColor:'#000000',align:'center'},
        text:    {w:200,h:16,text:'Custom text',fontSize:8,bold:false,italic:false,color:'#333333',bg:'',borderColor:'',align:'left'},
        pagebox: {w:145,h:16,text:'Page {page} of {pages}',fontSize:7,bold:true,italic:true,color:'#000000',bg:'',borderColor:'',align:'right'},
        divider: {w:860,h:4,text:'',fontSize:0,bold:false,italic:false,color:'#000000',bg:'',borderColor:'',align:'left',thickness:1},
    };
    function addEl(type) {
        const def = EL_DEF[type]||{w:150,h:18,text:'',fontSize:8,bold:false,italic:false,color:'#000000',bg:'',borderColor:'',align:'left'};
        const el = {id:++d.elCtr, type, x:20, y:20, ...JSON.parse(JSON.stringify(def))};
        if (type==='logo' && d.logoUrl) el.logoUrl=d.logoUrl;
        d.cfg.header.elements.push(el);
        d.selEl=el.id;
        renderHeader(); renderElList();
    }
    function removeEl(id) {
        d.cfg.header.elements=d.cfg.header.elements.filter(e=>e.id!==id);
        if(d.selEl===id)d.selEl=null;
        renderHeader();renderElList();
    }
    function updEl(id,k,v){const el=d.cfg.header.elements.find(e=>e.id===id);if(el){el[k]=v;renderHeader();renderElList();}}

    function startDrag(e,id){
        if(e.target.classList.contains('hd'))return;
        e.preventDefault();
        d.dragging=id;d.selEl=id;
        const div=document.querySelector(`[data-eid="${id}"]`);
        const r=div.getBoundingClientRect();
        d.dragOx=(e.clientX-r.left)/d.zoom;d.dragOy=(e.clientY-r.top)/d.zoom;
        document.addEventListener('mousemove',onDrag);document.addEventListener('mouseup',stopDrag);
        renderElList();
    }
    function onDrag(e){
        if(!d.dragging)return;
        const el=d.cfg.header.elements.find(x=>x.id===d.dragging);if(!el)return;
        const zone=document.getElementById('zh');const cr=zone.getBoundingClientRect();
        el.x=Math.max(0,Math.round((e.clientX-cr.left)/d.zoom-d.dragOx));
        el.y=Math.max(0,Math.round((e.clientY-cr.top)/d.zoom-d.dragOy));
        const div=document.querySelector(`[data-eid="${d.dragging}"]`);
        if(div){div.style.left=el.x+'px';div.style.top=el.y+'px';}
    }
    function stopDrag(){d.dragging=null;document.removeEventListener('mousemove',onDrag);document.removeEventListener('mouseup',stopDrag);}

    function renderHeader(){
        const zone=document.getElementById('zh');
        zone.style.minHeight=d.cfg.header.height+'px';
        zone.querySelectorAll('.hel').forEach(e=>e.remove());
        d.cfg.header.elements.forEach(el=>{
            const div=document.createElement('div');
            div.className='hel'+(d.selEl===el.id?' on':'');
            div.dataset.eid=el.id;
            div.style.cssText=`left:${el.x}px;top:${el.y}px;width:${el.w}px;min-height:${el.h||18}px;`;
            const fw=el.bold?'bold':'normal',fi=el.italic?'italic':'normal',fs=(el.fontSize||8)+'pt';
            const bgS=el.bg?`background:${el.bg};`:'';
            const bdS=el.borderColor?`border:1px solid ${el.borderColor};padding:2px 5px;`:'';
            let inner='';
            if(el.type==='logo'){
                const src=el.logoUrl||d.logoUrl||'';
                inner=src?`<img src="${src}" style="max-height:${el.h||55}px;max-width:${el.w||70}px;object-fit:contain;display:block">`
                    :`<div style="width:${el.w}px;height:${el.h}px;background:#e2e8f0;border-radius:3px;display:flex;align-items:center;justify-content:center;font-size:9px;color:#94a3b8">LOGO</div>`;
            }else if(el.type==='divider'){
                inner=`<div style="border-top:${el.thickness||1}pt solid ${el.color};width:100%"></div>`;
            }else{
                const underline=el.type==='title'?'text-decoration:underline;text-transform:uppercase;':'';
                inner=`<div style="font-size:${fs};font-weight:${fw};font-style:${fi};color:${el.color};text-align:${el.align||'left'};width:100%;white-space:pre-line;${bgS}${bdS}${underline}">${el.text||''}</div>`;
            }
            inner+=`<div class="hd" onclick="removeEl(${el.id})">✕</div>`;
            div.innerHTML=inner;
            div.addEventListener('mousedown',e=>startDrag(e,el.id));
            div.addEventListener('click',e=>{e.stopPropagation();d.selEl=el.id;renderElList();document.querySelectorAll('.hel').forEach(x=>x.classList.remove('on'));div.classList.add('on');});
            zone.appendChild(div);
        });
    }

    function renderElList(){
        const c=document.getElementById('elList');c.innerHTML='';
        if(!d.cfg.header.elements.length){c.innerHTML='<div style="font-size:.68rem;color:#94a3b8;text-align:center;padding:7px">No elements. Use palette above.</div>';return;}
        d.cfg.header.elements.forEach(el=>{
            const row=document.createElement('div');row.className='el-row'+(d.selEl===el.id?' on':'');
            row.innerHTML=`<span class="el-badge">${el.type}</span>
            <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.68rem">${el.type==='logo'?'[Logo]':(el.text?.substring(0,24)||'...')}</span>
            <button class="sbtn pd" onclick="event.stopPropagation();removeEl(${el.id})">✕</button>`;
            row.onclick=e=>{if(e.target.tagName==='BUTTON')return;d.selEl=el.id;renderElList();};
            c.appendChild(row);
            if(d.selEl===el.id){
                const ed=document.createElement('div');ed.className='ped';
                let h='';
                if(el.type!=='logo'&&el.type!=='divider'){
                    h+=`<div class="sr"><div class="sc"><label>Text (tokens: {report_name} {print_date} {company_name} {as_on_date})</label>
                    <textarea rows="2" class="si" style="resize:none;font-family:monospace;font-size:.67rem" onchange="updEl(${el.id},'text',this.value)">${el.text||''}</textarea></div></div>
                <div class="sr">
                    <div class="sc"><label>Font size</label><input type="number" value="${el.fontSize||8}" min="5" max="36" class="si" onchange="updEl(${el.id},'fontSize',+this.value)"></div>
                    <div class="sc"><label>Text color</label><input type="color" value="${el.color||'#000'}" class="sclr" style="width:100%;height:24px" oninput="updEl(${el.id},'color',this.value)"></div>
                    <div class="sc"><label>Background</label><input type="color" value="${el.bg||'#ffffff'}" class="sclr" style="width:100%;height:24px" oninput="updEl(${el.id},'bg',this.value)">
                        <label style="display:flex;align-items:center;gap:2px;font-size:.6rem;cursor:pointer;margin-top:2px"><input type="checkbox" ${!el.bg?'checked':''} onchange="updEl(${el.id},'bg',this.checked?'':'#ffffff')"> Transparent</label></div>
                </div>
                <div class="sr">
                    <div class="sc"><label>Border color</label><input type="color" value="${el.borderColor||'#000000'}" class="sclr" style="width:100%;height:24px" oninput="updEl(${el.id},'borderColor',this.value)">
                        <label style="display:flex;align-items:center;gap:2px;font-size:.6rem;cursor:pointer;margin-top:2px"><input type="checkbox" ${!el.borderColor?'checked':''} onchange="updEl(${el.id},'borderColor',this.checked?'':'#000000')"> No border</label></div>
                    <div class="sc"><label>Align</label>
                        <select class="ssel" onchange="updEl(${el.id},'align',this.value)">
                            ${['left','center','right'].map(a=>`<option ${el.align===a?'selected':''}>${a}</option>`).join('')}
                        </select></div>
                </div>
                <div class="sr" style="gap:10px">
                    <label style="font-size:.7rem;display:flex;align-items:center;gap:3px;cursor:pointer;margin:0"><input type="checkbox" ${el.bold?'checked':''} onchange="updEl(${el.id},'bold',this.checked)"> Bold</label>
                    <label style="font-size:.7rem;display:flex;align-items:center;gap:3px;cursor:pointer;margin:0"><input type="checkbox" ${el.italic?'checked':''} onchange="updEl(${el.id},'italic',this.checked)"> Italic</label>
                </div>`;
                }else if(el.type==='divider'){
                    h+=`<div class="sr">
                    <div class="sc"><label>Color</label><input type="color" value="${el.color||'#000'}" class="sclr" style="width:100%;height:24px" oninput="updEl(${el.id},'color',this.value)"></div>
                    <div class="sc"><label>Thickness pt</label><input type="number" value="${el.thickness||1}" min="1" max="8" class="si" oninput="updEl(${el.id},'thickness',+this.value)"></div>
                </div>`;
                }
                h+=`<div class="sr" style="margin-top:4px">
                <div class="sc"><label>X</label><input type="number" value="${el.x}" class="si" onchange="updEl(${el.id},'x',+this.value)"></div>
                <div class="sc"><label>Y</label><input type="number" value="${el.y}" class="si" onchange="updEl(${el.id},'y',+this.value)"></div>
                <div class="sc"><label>W</label><input type="number" value="${el.w}" class="si" onchange="updEl(${el.id},'w',+this.value)"></div>
                <div class="sc"><label>H</label><input type="number" value="${el.h}" class="si" onchange="updEl(${el.id},'h',+this.value)"></div>
            </div>`;
                ed.innerHTML=h;c.appendChild(ed);
            }
        });
    }

    // LOGO
    function onLDrop(e){e.preventDefault();e.currentTarget.classList.remove('dg');const f=e.dataTransfer.files[0];if(f&&f.type.startsWith('image/'))readLogo(f);}
    function onLFile(e){const f=e.target.files[0];if(f)readLogo(f);}
    function readLogo(file){
        const r=new FileReader();
        r.onload=ev=>{
            d.logoUrl=ev.target.result;
            document.getElementById('ldropInner').innerHTML=`<img src="${d.logoUrl}" style="max-height:48px;max-width:140px;object-fit:contain">`;
            let el=d.cfg.header.elements.find(e=>e.type==='logo');
            if(!el){addEl('logo');el=d.cfg.header.elements.find(e=>e.type==='logo');}
            if(el)el.logoUrl=d.logoUrl;
            renderHeader();
        };
        r.readAsDataURL(file);
    }
    function updLogo(){
        const w=+document.getElementById('lw').value,h=+document.getElementById('lh').value;
        const el=d.cfg.header.elements.find(e=>e.type==='logo');if(el){el.w=w;el.h=h;renderHeader();}
    }

    // ═══════════════════════════════════════════════════════════════
    // STYLE PANEL
    // ═══════════════════════════════════════════════════════════════
    const PRESETS=[
        {n:'Forest Green',c:{th_bg:'#008000',th_text:'#fff',sm_bg:'#006600',sm_text:'#fff',tot_bg:'#004400',tot_text:'#fff',even_bg:'#fff',odd_bg:'#f0fff0',border_color:'#bbddbb'}},
        {n:'Navy Blue',   c:{th_bg:'#1a3a5c',th_text:'#fff',sm_bg:'#152d47',sm_text:'#fff',tot_bg:'#0d2035',tot_text:'#fff',even_bg:'#fff',odd_bg:'#f0f5ff',border_color:'#c0d0e8'}},
        {n:'Deep Purple', c:{th_bg:'#4a1a7c',th_text:'#fff',sm_bg:'#3a1060',sm_text:'#fff',tot_bg:'#2d0d4e',tot_text:'#fff',even_bg:'#fff',odd_bg:'#faf0ff',border_color:'#d8c0f0'}},
        {n:'Teal',        c:{th_bg:'#0f766e',th_text:'#fff',sm_bg:'#0c5e58',sm_text:'#fff',tot_bg:'#094f49',tot_text:'#fff',even_bg:'#fff',odd_bg:'#f0fdf8',border_color:'#b0e0d8'}},
        {n:'Maroon',      c:{th_bg:'#881337',th_text:'#fff',sm_bg:'#6d0e2c',sm_text:'#fff',tot_bg:'#500820',tot_text:'#fff',even_bg:'#fff',odd_bg:'#fff0f3',border_color:'#f0b0bb'}},
        {n:'Charcoal',    c:{th_bg:'#1e293b',th_text:'#fff',sm_bg:'#2d3f55',sm_text:'#fff',tot_bg:'#0f172a',tot_text:'#fff',even_bg:'#fff',odd_bg:'#f8fafc',border_color:'#e0e8f0'}},
    ];
    const CFIELDS=[
        {k:'th_bg',l:'Table header bg'},{k:'th_text',l:'Table header text'},
        {k:'tot_bg',l:'Total row bg'},{k:'tot_text',l:'Total row text'},
        {k:'even_bg',l:'Even row'},{k:'odd_bg',l:'Odd row'},{k:'border_color',l:'Border color'},
    ];
    const STOGGS=[
        {k:'show_totals',l:'Grand total row'},{k:'show_row_numbers',l:'Row numbers'},
        {k:'zebra_rows',l:'Alternating rows'},{k:'indent_customers',l:'Indent customer names'},
        {k:'show_borders',l:'Cell borders'},
    ];
    function buildStylePanel(){
        const pb=document.getElementById('cpBtns');pb.innerHTML='';
        PRESETS.forEach(p=>{
            const b=document.createElement('div');b.className='cpill';
            b.innerHTML=[p.c.th_bg,p.c.sm_bg,p.c.odd_bg].map(h=>`<span style="width:9px;height:9px;border-radius:50%;background:${h};display:inline-block;border:1px solid rgba(0,0,0,.1)"></span>`).join('')+`<span style="color:#64748b">${p.n}</span>`;
            b.onclick=()=>{Object.assign(d.cfg.style,p.c);buildColorInputs();renderData();syncStyleSidebar();};
            pb.appendChild(b);
        });
        buildColorInputs();
        const tc=document.getElementById('stoggles');tc.innerHTML='';
        STOGGS.forEach(f=>{
            const dv=document.createElement('div');dv.className='stog';
            dv.innerHTML=`<label>${f.l}</label><input type="checkbox" ${d.cfg.style[f.k]!==false?'checked':''} onchange="d.cfg.style['${f.k}']=this.checked;renderData()">`;
            tc.appendChild(dv);
        });
        syncStyleSidebar();
    }
    function buildColorInputs(){
        const cg=document.getElementById('cgrid');cg.innerHTML='';
        CFIELDS.forEach(f=>{
            const dv=document.createElement('div');
            dv.style.cssText='display:flex;align-items:center;gap:7px;padding:3px 0;border-bottom:.5px solid #f1f5f9';
            dv.innerHTML=`<span style="flex:1;font-size:.7rem;color:#374151">${f.l}</span>
            <input type="color" class="sclr" id="gc_${f.k}" value="${d.cfg.style[f.k]||'#000'}"
                   oninput="d.cfg.style['${f.k}']=this.value;renderData()">`;
            cg.appendChild(dv);
        });
    }
    function syncStyleSidebar(){
        document.getElementById('g-smbg').value  = d.cfg.style.sm_bg  ||'#006600';
        document.getElementById('g-smtext').value= d.cfg.style.sm_text||'#ffffff';
        CFIELDS.forEach(f=>{ const el=document.getElementById('gc_'+f.k); if(el)el.value=d.cfg.style[f.k]||'#000000'; });
    }

    // FOOTER
    function renderFooter(){
        const f=d.cfg.footer;
        const fz=z=>{if(z==='company')return CO.company_name||'Company';if(z==='report_name')return '{{ $report->name }}';if(z==='datetime')return new Date().toLocaleDateString();if(z==='page')return 'Page 1 of 1';return '';};
        document.getElementById('pfL').textContent=fz(f.left);
        document.getElementById('pfC').textContent=fz(f.center);
        document.getElementById('pfR').textContent=fz(f.right);
        document.getElementById('zf').style.borderTop=f.show_divider?'1px solid #e8edf5':'none';
        // Restore selects
        ['left','center','right'].forEach(z=>{const s=document.getElementById('fz_'+z);if(s)s.value=f[z]||'company';});
    }

    // DATASETS PANEL
    function renderDsList(){
        const c=document.getElementById('dsList');c.innerHTML='';
        d.cfg.datasets.slice(1).forEach((ds,i)=>{
            const b=document.createElement('div');
            b.style.cssText='border:1px solid #e2e8f0;border-radius:5px;margin-bottom:5px;overflow:hidden';
            b.innerHTML=`<div style="background:#f8fafc;padding:5px 8px;display:flex;align-items:center;gap:5px;cursor:pointer;border-bottom:1px solid #e2e8f0;font-size:.72rem;font-weight:500" onclick="this.nextSibling.classList.toggle('open')">
            <i class="bi bi-table" style="color:#2563eb;font-size:.75rem"></i>
            <span style="flex:1">Extra Table ${i+1}: ${ds.title||'untitled'}</span>
            <button class="sbtn pd" onclick="event.stopPropagation();rmDs(${ds.id})">✕</button>
        </div>
        <div style="padding:9px;display:none">
            <label style="font-size:.62rem;color:#64748b;display:block;margin-bottom:2px">Title</label>
            <input type="text" class="si" value="${ds.title||''}" style="margin-bottom:5px" oninput="updDs(${ds.id},'title',this.value)">
            <label style="font-size:.62rem;color:#64748b;display:block;margin-bottom:2px">SQL (optional — blank = main query)</label>
            <textarea class="si" rows="3" style="font-family:monospace;font-size:.62rem;resize:vertical" oninput="updDs(${ds.id},'sql_query',this.value)">${ds.sql_query||''}</textarea>
            <label style="font-size:.62rem;color:#64748b;display:block;margin-bottom:2px;margin-top:5px">Group column (SQL alias)</label>
            <input type="text" class="si" value="${ds.group_column||''}" placeholder="salesman_name" oninput="updDs(${ds.id},'group_column',this.value)">
        </div>`;
            c.appendChild(b);
        });
    }
    function addDataset(){d.cfg.datasets.push({id:d.dsCtr++,title:'Extra Table',show_title:true,date_label:'',date_value:'',sql_query:'',group_column:'',columns:[]});renderDsList();}
    function rmDs(id){d.cfg.datasets=d.cfg.datasets.filter(x=>x.id!==id);renderDsList();}
    function updDs(id,k,v){const ds=d.cfg.datasets.find(x=>x.id===id);if(ds)ds[k]=v;}

    // ═══════════════════════════════════════════════════════════════
    // PREVIEW TOGGLE
    // ═══════════════════════════════════════════════════════════════
    function togglePreview(){
        d.preview=!d.preview;
        document.getElementById('pvTxt').textContent=d.preview?'Edit':'Preview';
        const zh=document.getElementById('zh');
        zh.classList.toggle('editing',!d.preview);
        document.getElementById('zlbl_zh')||0; // suppress
        document.querySelector('.zlbl').style.display=d.preview?'none':'';
        document.querySelectorAll('.hel').forEach(el=>el.style.border=d.preview?'none':'');
        document.querySelectorAll('[data-ci]').forEach(th=>th.style.cursor=d.preview?'default':'pointer');
    }

    // ═══════════════════════════════════════════════════════════════
    // SAVE
    // ═══════════════════════════════════════════════════════════════
    async function saveDesign(){
        const btn=document.getElementById('saveBtn');
        btn.className='btn-tb save saving';btn.innerHTML='<i class="bi bi-cloud-upload"></i> Saving…';
        // Sync active columns back to datasets[0]
        // (already in sync, but double-check)
        try{
            const resp=await fetch(SAVE_URL,{
                method:'PUT',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF},
                body:JSON.stringify({designer_config:JSON.stringify(d.cfg)})
            });
            const data=await resp.json();
            if(data.success){
                btn.className='btn-tb save saved';btn.innerHTML='<i class="bi bi-check-circle-fill"></i> Saved!';
                document.getElementById('topStatus').textContent='Last saved '+new Date().toLocaleTimeString();
                showToast('Design saved successfully');
                setTimeout(()=>{btn.className='btn-tb save';btn.innerHTML='<i class="bi bi-cloud-check"></i> Save Design';},2500);
            }else throw new Error(data.error||'Failed');
        }catch(e){
            btn.className='btn-tb save';btn.innerHTML='<i class="bi bi-cloud-check"></i> Save Design';
            showToast('Error: '+e.message,true);
        }
    }
    function showToast(msg,err=false){
        const t=document.getElementById('toast');
        t.textContent=msg;t.style.background=err?'#dc2626':'#1e3a5c';
        t.classList.add('on');setTimeout(()=>t.classList.remove('on'),3000);
    }

    // ═══════════════════════════════════════════════════════════════
    // INIT
    // ═══════════════════════════════════════════════════════════════
    function init(){
        buildLayoutGrid();
        buildStylePanel();
        renderHeader();
        renderDsList();
        renderFooter();
        renderColList();
        renderParamsList();        // <-- Add this line
        renderParamsBar();         // <-- Add this line

        if (EXIST && d.cfg.layout) {
            // Restore existing design
            renderData();
            document.getElementById('zp').style.display='';
            document.getElementById('zf').style.display='';
            renderFooter();
            // restore canvas height
            document.getElementById('chr').value = d.cfg.header.height||90;
            document.getElementById('chv').textContent = (d.cfg.header.height||90)+'pt';
            document.getElementById('topStatus').textContent='Design loaded';
        }
        setTimeout(autoFit, 300);
    }
    init();
</script>
</body>
</html>
