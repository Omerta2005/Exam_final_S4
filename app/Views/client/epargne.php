                    <form action="<?= base_url('client/setEpargne') ?>" 
                          method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Configurer votre epargne
                            </label>
                            <input 
                                type="number"
                                name="epargne"
                                class="form-control form-control-lg"
                                placeholder="20"
                                required
                            >
                        </div>
                        <button type="submit" 
                                class="btn btn-primary btn-lg w-100 rounded-3">

                            configurer
                        </button>
                    </form>