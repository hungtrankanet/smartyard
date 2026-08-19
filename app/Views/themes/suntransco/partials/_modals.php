<!-- Cổng Thành Viên -->
<div class="modal-overlay" id="authModal">
    <div class="modal-card card-corporate" style="max-width: 520px;">
        <button class="modal-close close-btn" onclick="closeModal('authModal')">&times;</button>
        <div class="modal-header text-center" style="margin-bottom: 24px;">
            <h3><i class="fa-solid fa-building-shield text-primary"></i> 
                <span class="lang-vi">Cổng Thành Viên Doanh Nghiệp</span>
                <span class="lang-en">Member Portal</span>
            </h3>
            <p style="font-size: 0.82rem; color: var(--text-muted); margin-top: 6px;">
                <span class="lang-vi">Mạng lưới kết nối giao thương Logistics & Đối tác xuất nhập khẩu</span>
                <span class="lang-en">B2B Logistics & Partner Network</span>
            </p>
        </div>
        <div class="grid grid-2" style="gap: 16px;">
            <div class="card-corporate text-center" style="cursor:pointer; padding: 24px 16px; border: 1.5px solid var(--border); transition: all 0.2s;" onclick="location.href='<?= langBaseUrl('member/login'); ?>'">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(30,58,138,0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 12px;">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </div>
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 6px;">
                    <span class="lang-vi">Đăng Nhập</span>
                    <span class="lang-en">Log In</span>
                </h4>
                <p style="font-size:0.75rem;color:var(--text-muted);margin-top: 4px; line-height: 1.4; margin-bottom: 14px;">
                    <span class="lang-vi">Truy cập bảng điều khiển thành viên</span>
                    <span class="lang-en">Sign in to member dashboard</span>
                </p>
                <button type="button" class="btn btn-primary btn-sm" style="width: 100%;">
                    <span class="lang-vi">Đăng Nhập Ngay</span>
                    <span class="lang-en">Sign In</span>
                </button>
            </div>
            <div class="card-corporate text-center" style="cursor:pointer; padding: 24px 16px; border: 1.5px solid var(--border); transition: all 0.2s;" onclick="location.href='<?= langBaseUrl('member/register'); ?>'">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(22,163,74,0.1); color: #16a34a; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 12px;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 6px; color: #16a34a;">
                    <span class="lang-vi">Đăng Ký Thành Viên</span>
                    <span class="lang-en">Register</span>
                </h4>
                <p style="font-size:0.75rem;color:var(--text-muted);margin-top: 4px; line-height: 1.4; margin-bottom: 14px;">
                    <span class="lang-vi">Xác thực OTP & nhận đặc quyền đối tác</span>
                    <span class="lang-en">Join with Email OTP</span>
                </p>
                <button type="button" class="btn btn-outline btn-sm" style="width: 100%; border-color: #16a34a; color: #16a34a;">
                    <span class="lang-vi">Đăng Ký (OTP)</span>
                    <span class="lang-en">Register</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đăng Ký Mới -->
<div class="modal-overlay" id="memberModal">
    <div class="modal-card card-corporate">
        <button class="modal-close close-btn" onclick="closeModal('memberModal')">&times;</button>
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus text-primary"></i> 
                <span class="lang-vi">Đăng Ký Đối Tác Mạng Lưới</span>
                <span class="lang-en">Register Network Partner</span>
            </h3>
        </div>
        <form id="memberRegisterForm" action="<?= base_url('api/contact'); ?>" method="post" onsubmit="event.preventDefault(); alert('Cảm ơn bạn! Thông tin đăng ký đã được ghi nhận.'); closeModal('memberModal');">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label>
                    <span class="lang-vi">Tên doanh nghiệp *</span>
                    <span class="lang-en">Company Name *</span>
                </label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>
                    <span class="lang-vi">Email liên hệ *</span>
                    <span class="lang-en">Contact Email *</span>
                </label>
                <input type="email" name="email" required>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('memberModal'); openModal('authModal')">
                    <span class="lang-vi">Quay lại</span>
                    <span class="lang-en">Back</span>
                </button>
                <button type="submit" class="btn btn-primary">
                    <span class="lang-vi">Gửi Đăng Ký</span>
                    <span class="lang-en">Submit Application</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- RFQ Modal -->
<div class="modal-overlay" id="rfqModal">
    <div class="modal-card card-corporate" style="max-width: 550px;">
        <button class="modal-close close-btn" onclick="closeModal('rfqModal')">&times;</button>
        <div class="modal-header">
            <h3><i class="fa-solid fa-file-invoice-dollar text-primary"></i> 
                <span class="lang-vi">Yêu Cầu Báo Giá Nhanh (RFQ)</span>
                <span class="lang-en">Quick Freight Quote (RFQ)</span>
            </h3>
            <p style="font-size: 0.78rem; color: var(--text-muted); margin-top: 4px;">
                <span class="lang-vi">Điền thông tin hành trình và phương thức liên hệ để nhận cước phí tối ưu trong vòng 5 phút.</span>
                <span class="lang-en">Enter shipment details to receive competitive rate quotes within 5 minutes.</span>
            </p>
        </div>
        <form id="rfqForm" action="<?= base_url('api/contact'); ?>" method="post" onsubmit="event.preventDefault(); alert('Cảm ơn bạn! Yêu cầu báo giá đã được gửi.'); closeModal('rfqModal');" style="margin-top: 15px;">
            <?= csrf_field(); ?>
            <div class="grid grid-2 gap-md" style="margin-bottom: 12px;">
                <div class="form-group">
                    <label>
                        <span class="lang-vi">Loại hình vận tải</span>
                        <span class="lang-en">Shipment Mode</span>
                    </label>
                    <div class="select-wrapper">
                        <select name="type" id="rfq_type" required style="width:100%; height:44px; padding:0 16px; border:1.5px solid var(--border); border-radius:var(--radius-xs); font-family:'Montserrat',sans-serif; font-size:0.8rem; background:var(--bg-light); appearance:none; -webkit-appearance:none;">
                            <option value="FCL">FCL (Full Container)</option>
                            <option value="LCL">LCL (Consolidation)</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        <span class="lang-vi">Cảng đi *</span>
                        <span class="lang-en">Origin Port (POL) *</span>
                    </label>
                    <input type="text" name="pol" id="rfq_pol" required style="width:100%; height:44px; padding:0 16px; border:1.5px solid var(--border); border-radius:var(--radius-xs); font-family:'Montserrat',sans-serif; font-size:0.8rem; background:var(--bg-light);">
                </div>
            </div>
            <div class="grid grid-2 gap-md" style="margin-bottom: 12px;">
                <div class="form-group">
                    <label>
                        <span class="lang-vi">Cảng đến *</span>
                        <span class="lang-en">Destination Port (POD) *</span>
                    </label>
                    <input type="text" name="pod" id="rfq_pod" required style="width:100%; height:44px; padding:0 16px; border:1.5px solid var(--border); border-radius:var(--radius-xs); font-family:'Montserrat',sans-serif; font-size:0.8rem; background:var(--bg-light);">
                </div>
                <div class="form-group">
                    <label>
                        <span class="lang-vi">Họ và tên *</span>
                        <span class="lang-en">Full Name *</span>
                    </label>
                    <input type="text" name="name" id="rfq_name" required style="width:100%; height:44px; padding:0 16px; border:1.5px solid var(--border); border-radius:var(--radius-xs); font-family:'Montserrat',sans-serif; font-size:0.8rem; background:var(--bg-light);">
                </div>
            </div>
            <div class="grid grid-2 gap-md" style="margin-bottom: 15px;">
                <div class="form-group">
                    <label>
                        <span class="lang-vi">Email liên hệ *</span>
                        <span class="lang-en">Email Address *</span>
                    </label>
                    <input type="email" name="email" id="rfq_email" required style="width:100%; height:44px; padding:0 16px; border:1.5px solid var(--border); border-radius:var(--radius-xs); font-family:'Montserrat',sans-serif; font-size:0.8rem; background:var(--bg-light);">
                </div>
                <div class="form-group">
                    <label>
                        <span class="lang-vi">Số điện thoại *</span>
                        <span class="lang-en">Phone Number *</span>
                    </label>
                    <input type="text" name="phone" id="rfq_phone" required style="width:100%; height:44px; padding:0 16px; border:1.5px solid var(--border); border-radius:var(--radius-xs); font-family:'Montserrat',sans-serif; font-size:0.8rem; background:var(--bg-light);">
                </div>
            </div>
            <div class="form-actions" style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('rfqModal')">
                    <span class="lang-vi">Hủy</span>
                    <span class="lang-en">Cancel</span>
                </button>
                <button type="submit" class="btn btn-primary">
                    <span class="lang-vi">Gửi Yêu Cầu</span>
                    <span class="lang-en">Submit Quote</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- AI Chatbot Assistant Modal -->
<div class="modal-overlay" id="aiChatModal">
    <div class="modal-card card-corporate" style="max-width: 580px; padding: 0; overflow: hidden; border-radius: 18px;">
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #1e3a8a, #0b1329); padding: 20px 24px; color: #fff; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; box-shadow: 0 0 15px rgba(59,130,246,0.5);">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #fff;">
                        <span class="lang-vi">Trợ Lý Logistics AI TOP BEST GLOBAL</span>
                        <span class="lang-en">TOP BEST GLOBAL AI Logistics Assistant</span>
                    </h4>
                    <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.72rem; color: #4ade80;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #4ade80; display: inline-block;"></span>
                        <span class="lang-vi">Trực tuyến 24/7 • Tư vấn giá & HS Code</span>
                        <span class="lang-en">Online 24/7 • Instant Quote & HS Code</span>
                    </span>
                </div>
            </div>
            <button class="modal-close close-btn" onclick="closeModal('aiChatModal')" style="position: static; color: #fff; font-size: 1.5rem;">&times;</button>
        </div>

        <!-- Chat Body -->
        <div id="aiChatMessages" style="padding: 20px 24px; height: 320px; overflow-y: auto; background: #070e1f; display: flex; flex-direction: column; gap: 14px;">
            <!-- Message Bot -->
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: #2563eb; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; flex-shrink: 0;">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); padding: 12px 16px; border-radius: 4px 14px 14px 14px; max-width: 82%; font-size: 0.83rem; line-height: 1.55; color: #e2e8f0;">
                    <span class="lang-vi">Xin chào Quý khách! Tôi là trợ lý AI của <strong>TOP BEST GLOBAL</strong>. Quý khách muốn nhận báo giá cước tuyến nào hoặc cần tra cứu mã HS Code mặt hàng gì?</span>
                    <span class="lang-en">Hello! I am the <strong>TOP BEST GLOBAL AI Assistant</strong>. How can I help you today with freight rate quotes or HS Code lookup?</span>
                </div>
            </div>

            <!-- Quick Suggestion Buttons -->
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-left: 42px;">
                <button type="button" onclick="document.getElementById('aiChatInput').value='Báo giá cước FCL Hải Phòng đi Los Angeles'; document.getElementById('aiChatSendBtn').click();" style="background: rgba(37,99,235,0.15); border: 1px solid rgba(37,99,235,0.4); color: #93c5fd; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; cursor: pointer; transition: all 0.2s;">
                    🚢 Báo giá cước Hải Phòng - Mỹ
                </button>
                <button type="button" onclick="document.getElementById('aiChatInput').value='Tra cứu mã HS code linh kiện điện tử'; document.getElementById('aiChatSendBtn').click();" style="background: rgba(147,51,234,0.15); border: 1px solid rgba(147,51,234,0.4); color: #d8b4fe; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; cursor: pointer; transition: all 0.2s;">
                    📑 Tra cứu mã HS Code
                </button>
                <button type="button" onclick="closeModal('aiChatModal'); openModal('rfqModal');" style="background: rgba(22,163,74,0.15); border: 1px solid rgba(22,163,74,0.4); color: #86efac; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; cursor: pointer; transition: all 0.2s;">
                    ⚡ Điền Form Báo Giá RFQ
                </button>
            </div>
        </div>

        <!-- Chat Input Form -->
        <form id="aiChatForm" onsubmit="event.preventDefault(); window.sendAiChatMessage();" style="padding: 14px 20px; background: #0b1329; border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 10px; align-items: center;">
            <input type="text" id="aiChatInput" placeholder="Nhập câu hỏi hoặc yêu cầu báo giá..." required style="flex: 1; height: 42px; padding: 0 16px; border: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.06); color: #fff; border-radius: 22px; font-size: 0.83rem; outline: none;">
            <button type="submit" id="aiChatSendBtn" style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #7c3aed); border: none; color: #fff; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
window.sendAiChatMessage = function() {
    var input = document.getElementById('aiChatInput');
    var msg = input.value.trim();
    if (!msg) return;

    var container = document.getElementById('aiChatMessages');
    
    // Append User Message
    var userDiv = document.createElement('div');
    userDiv.style.cssText = "display: flex; gap: 10px; align-items: flex-start; justify-content: flex-end;";
    userDiv.innerHTML = '<div style="background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; padding: 10px 16px; border-radius: 14px 4px 14px 14px; max-width: 80%; font-size: 0.83rem; line-height: 1.55;">' + msg.replace(/</g, "&lt;") + '</div>';
    container.appendChild(userDiv);
    input.value = '';
    container.scrollTop = container.scrollHeight;

    // Bot Typing Indicator & Reply
    setTimeout(function() {
        var botDiv = document.createElement('div');
        botDiv.style.cssText = "display: flex; gap: 10px; align-items: flex-start;";
        botDiv.innerHTML = '<div style="width: 32px; height: 32px; border-radius: 50%; background: #2563eb; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.85rem; flex-shrink: 0;"><i class="fa-solid fa-robot"></i></div><div style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12); padding: 12px 16px; border-radius: 4px 14px 14px 14px; max-width: 82%; font-size: 0.83rem; line-height: 1.55; color: #e2e8f0;">Đã ghi nhận yêu cầu của Quý khách! Chuyên viên TOP BEST GLOBAL sẽ liên hệ tư vấn lộ trình và gửi biểu cước ưu đãi nhất qua Hotline/Email trong vòng 5 phút. Quý khách cũng có thể liên hệ trực tiếp qua Zalo/Hotline: <strong>+84.28.39971199</strong>.</div>';
        container.appendChild(botDiv);
        container.scrollTop = container.scrollHeight;
    }, 600);
};
</script>
