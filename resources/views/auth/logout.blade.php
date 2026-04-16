<!-- MODAL LOGOUT -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content" style="border-radius:16px; border:none;">
      <div class="modal-body text-center p-4">

        <div class="text-danger mb-3">
          <i class="bi bi-box-arrow-right" style="font-size: 3rem;"></i>
        </div>

        <h5 class="fw-bold mb-2">Konfirmasi Logout</h5>
        <p class="text-muted small mb-4">
          Apakah Anda yakin ingin keluar?
        </p>

        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">
            Batal
          </button>

          <button type="button"
            class="btn btn-danger w-100"
            onclick="document.getElementById('logout-form').submit()">
            Ya, Logout
          </button>
        </div>

        <!-- FORM LOGOUT -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>

      </div>
    </div>
  </div>
</div>