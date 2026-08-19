<!-- TOP BEST GLOBAL - Luxury OTP Verification Modal -->
<div class="modal fade" id="tbgVotingOtpModal" tabindex="-1" role="dialog" aria-labelledby="tbgVotingOtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid #D4AF37; box-shadow: 0 20px 40px rgba(10,25,47,0.25); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0A192F 0%, #1e293b 100%); padding: 20px 24px; border-bottom: 2px solid #D4AF37;">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <div>
                        <span class="badge" style="background: #D4AF37; color: #0A192F; font-weight: 700; font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">XÁC THỰC CỬ TRI</span>
                        <h5 class="modal-title text-white font-weight-bold" id="tbgVotingOtpModalLabel" style="margin-top: 5px; font-size: 18px;">
                            Bình Chọn Ứng Viên Tiêu Biểu
                        </h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; font-size: 28px; text-shadow: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="modal-body" style="padding: 24px; background: #ffffff;">
                <div class="d-flex align-items-center mb-4 p-3" style="background: #faf8f5; border: 1px solid #f0e6d2; border-radius: 10px;">
                    <div style="width: 50px; height: 50px; border-radius: 8px; background: #0A192F; display: flex; align-items: center; justify-content: center; color: #D4AF37; font-size: 20px; font-weight: bold; overflow: hidden; margin-right: 14px; flex-shrink: 0;" id="tbgModalAvatarBox">
                        <i class="fa fa-award"></i>
                    </div>
                    <div style="flex-grow: 1; overflow: hidden;">
                        <h6 class="font-weight-bold mb-1 text-truncate" id="tbgModalCandidateName" style="color: #0A192F; font-size: 15px;">Đang tải...</h6>
                        <div class="small text-muted" id="tbgModalCandidateCategory">Hạng mục vinh danh</div>
                    </div>
                </div>

                <div id="tbgVotingAlert" class="alert d-none" role="alert" style="font-size: 13px; border-radius: 8px;"></div>

                <form id="tbgStep1Form" onsubmit="return handleTbgSendOtp(event);">
                    <input type="hidden" id="tbgVoteCandidateId" name="candidate_id" value="">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark" style="font-size: 13px;">Địa chỉ Email của bạn <span class="text-danger">*</span></label>
                        <input type="email" id="tbgVoterEmail" name="email" class="form-control" placeholder="name@example.com" required style="height: 46px; border-radius: 8px; font-size: 14px; border: 1px solid #cbd5e1;">
                        <small class="form-text text-muted">Mã OTP 6 chữ số sẽ được gửi vào email này để xác thực tính minh bạch.</small>
                    </div>
                    <div class="form-group mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="tbgTermsCheck" required checked>
                            <label class="custom-control-label small text-muted" for="tbgTermsCheck">
                                Tôi cam kết bình chọn công tâm và đồng ý với Quy chế bình chọn.
                            </label>
                        </div>
                    </div>
                    <button type="submit" id="tbgBtnSendOtp" class="btn btn-block font-weight-bold text-dark" style="height: 48px; background: linear-gradient(135deg, #D4AF37 0%, #F3E5AB 50%, #AA771C 100%); border: none; border-radius: 8px; font-size: 15px; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(212,175,55,0.3);">
                        <i class="fa fa-paper-plane mr-1"></i> NHẬN MÃ OTP BÌNH CHỌN
                    </button>
                </form>

                <form id="tbgStep2Form" class="d-none" onsubmit="return handleTbgSubmitVote(event);">
                    <input type="hidden" id="tbgVoteSessionToken" name="token" value="">
                    <div class="text-center mb-3">
                        <div class="small text-muted mb-1">Mã xác thực 6 chữ số đã được gửi đến:</div>
                        <strong id="tbgTargetEmailDisplay" class="text-primary" style="font-size: 14px;"></strong>
                    </div>

                    <div class="form-group mb-3 text-center">
                        <label class="font-weight-bold text-dark d-block" style="font-size: 13px;">NHẬP MÃ OTP (6 CHỮ SỐ)</label>
                        <input type="text" id="tbgOtpCodeInput" name="otp_code" maxlength="6" pattern="[0-9]{6}" class="form-control text-center font-weight-bold" placeholder="• • • • • •" required style="height: 52px; font-size: 24px; letter-spacing: 8px; border-radius: 8px; border: 2px solid #D4AF37; max-width: 240px; margin: 0 auto; font-family: monospace;">
                        <div class="small text-muted mt-2">
                            Thời gian hiệu lực còn lại: <strong id="tbgOtpCountdown" class="text-danger font-weight-bold">05:00</strong>
                        </div>
                    </div>

                    <button type="submit" id="tbgBtnSubmitVote" class="btn btn-block font-weight-bold text-white mb-3" style="height: 48px; background: linear-gradient(135deg, #0A192F 0%, #1e3a8a 100%); border: none; border-radius: 8px; font-size: 15px; letter-spacing: 0.5px; box-shadow: 0 4px 14px rgba(10,25,47,0.3);">
                        <i class="fa fa-check-circle mr-1"></i> XÁC NHẬN BÌNH CHỌN
                    </button>

                    <div class="d-flex justify-content-between align-items-center small">
                        <button type="button" class="btn btn-link btn-sm text-muted p-0" onclick="tbgSwitchToStep(1)">
                            <i class="fa fa-arrow-left"></i> Đổi email
                        </button>
                        <button type="button" id="tbgBtnResendOtp" class="btn btn-link btn-sm text-primary p-0 font-weight-bold" disabled onclick="handleTbgResendOtp()">
                            Gửi lại mã (<span id="tbgResendTimer">60</span>s)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let tbgCountdownInterval = null, tbgResendInterval = null;
function getDeviceFingerprint(){try{const c=document.createElement("canvas"),ctx=c.getContext("2d");ctx.textBaseline="top";ctx.font="14px Arial";ctx.fillText("TOPBESTGLOBAL_2026",2,2);return btoa(c.toDataURL()).slice(0,32);}catch(e){return "fp_"+Math.random().toString(36).substring(2,15);}}
function openVotingModal(candidateId, candidateName, categoryName, avatarUrl){
    document.getElementById("tbgVoteCandidateId").value = candidateId;
    document.getElementById("tbgModalCandidateName").innerText = candidateName;
    document.getElementById("tbgModalCandidateCategory").innerText = categoryName || "Hạng mục quốc gia";
    const avBox = document.getElementById("tbgModalAvatarBox");
    avBox.innerHTML = avatarUrl ? `<img src="${avatarUrl}" style="width:100%;height:100%;object-fit:cover;">` : `<i class="fa fa-award"></i>`;
    tbgSwitchToStep(1);
    $("#tbgVotingOtpModal").modal("show");
}
function tbgSwitchToStep(step){
    const s1 = document.getElementById("tbgStep1Form"), s2 = document.getElementById("tbgStep2Form"), alertBox = document.getElementById("tbgVotingAlert");
    alertBox.className = "alert d-none";
    if(step === 1){ s1.classList.remove("d-none"); s2.classList.add("d-none"); clearInterval(tbgCountdownInterval); clearInterval(tbgResendInterval); }
    else { s1.classList.add("d-none"); s2.classList.remove("d-none"); document.getElementById("tbgOtpCodeInput").focus(); }
}
function showTbgAlert(msg, type){
    const a = document.getElementById("tbgVotingAlert");
    a.className = `alert alert-${type}`; a.innerHTML = msg; a.classList.remove("d-none");
}
function handleTbgSendOtp(e){
    e.preventDefault();
    const btn = document.getElementById("tbgBtnSendOtp"), email = document.getElementById("tbgVoterEmail").value.trim(), candidateId = document.getElementById("tbgVoteCandidateId").value;
    btn.disabled = true; btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> Đang gửi mã OTP...`;
    fetch("<?= base_url("voting/send-otp-ajax"); ?>", {
        method: "POST", credentials: "same-origin", headers: {"Content-Type": "application/x-www-form-urlencoded", "X-Requested-With": "XMLHttpRequest"},
        body: new URLSearchParams({ email: email, candidate_id: candidateId, fingerprint: getDeviceFingerprint() })
    }).then(r => r.json()).then(res => {
        btn.disabled = false; btn.innerHTML = `<i class="fa fa-paper-plane mr-1"></i> NHẬN MÃ OTP BÌNH CHỌN`;
        if(res.status === "success"){
            document.getElementById("tbgVoteSessionToken").value = res.token;
            document.getElementById("tbgTargetEmailDisplay").innerText = email;
            tbgSwitchToStep(2);
            startTbgCountdown(300); startTbgResendTimer(res.cooldown_seconds || 60);
        } else { showTbgAlert(res.message || "Lỗi gửi mã OTP. Vui lòng thử lại.", "danger"); }
    }).catch(err => { btn.disabled = false; btn.innerHTML = `<i class="fa fa-paper-plane mr-1"></i> NHẬN MÃ OTP BÌNH CHỌN`; showTbgAlert("Lỗi kết nối máy chủ.", "danger"); });
    return false;
}
function handleTbgSubmitVote(e){
    e.preventDefault();
    const btn = document.getElementById("tbgBtnSubmitVote"), token = document.getElementById("tbgVoteSessionToken").value,
          otpCode = document.getElementById("tbgOtpCodeInput").value.trim(), email = document.getElementById("tbgVoterEmail").value.trim(),
          candidateId = document.getElementById("tbgVoteCandidateId").value;
    btn.disabled = true; btn.innerHTML = `<i class="fa fa-spinner fa-spin"></i> Đang xác nhận...`;
    fetch("<?= base_url("voting/submit-vote-ajax"); ?>", {
        method: "POST", credentials: "same-origin", headers: {"Content-Type": "application/x-www-form-urlencoded", "X-Requested-With": "XMLHttpRequest"},
        body: new URLSearchParams({ token: token, otp_code: otpCode, email: email, candidate_id: candidateId, fingerprint: getDeviceFingerprint() })
    }).then(r => r.json()).then(res => {
        btn.disabled = false; btn.innerHTML = `<i class="fa fa-check-circle mr-1"></i> XÁC NHẬN BÌNH CHỌN`;
        if(res.status === "success"){
            showTbgAlert(`🎉 <strong>${res.message}</strong>`, "success");
            const counterEl = document.getElementById(`tbgVoteCounter_${candidateId}`);
            if(counterEl) counterEl.innerText = Number(res.public_votes).toLocaleString();
            setTimeout(() => { $("#tbgVotingOtpModal").modal("hide"); }, 2000);
        } else { showTbgAlert(res.message || "Xác thực thất bại.", "danger"); }
    }).catch(err => { btn.disabled = false; btn.innerHTML = `<i class="fa fa-check-circle mr-1"></i> XÁC NHẬN BÌNH CHỌN`; showTbgAlert("Lỗi xử lý hệ thống.", "danger"); });
    return false;
}
function startTbgCountdown(seconds){
    clearInterval(tbgCountdownInterval); let rem = seconds; const el = document.getElementById("tbgOtpCountdown");
    tbgCountdownInterval = setInterval(() => {
        rem--; const m = String(Math.floor(rem/60)).padStart(2,"0"), s = String(rem%60).padStart(2,"0");
        el.innerText = `${m}:${s}`;
        if(rem <= 0){ clearInterval(tbgCountdownInterval); el.innerText = "Hết hạn"; showTbgAlert("Mã OTP đã hết hạn. Vui lòng gửi lại mã mới.", "warning"); }
    }, 1000);
}
function startTbgResendTimer(seconds){
    clearInterval(tbgResendInterval); let rem = seconds; const btn = document.getElementById("tbgBtnResendOtp"), span = document.getElementById("tbgResendTimer");
    btn.disabled = true;
    tbgResendInterval = setInterval(() => {
        rem--; span.innerText = rem;
        if(rem <= 0){ clearInterval(tbgResendInterval); btn.disabled = false; btn.innerText = "Gửi lại mã OTP"; }
    }, 1000);
}
function handleTbgResendOtp(){ handleTbgSendOtp(new Event("submit")); }
</script>
